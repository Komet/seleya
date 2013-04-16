<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use kvibes\SeleyaBundle\Entity\Metadata;
use kvibes\SeleyaBundle\Entity\Record;
use kvibes\SeleyaBundle\Form\Type\RecordType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * The RecordController
 *
 * @package SeleyaBundle
 * @author  Daniel Kabel <daniel.kabel@me.com>
 * @version 1.0
 * 
 * @Route("/record")
 */
class RecordController extends Controller
{
    const RECORDS_PER_PAGE = 15;
    
    /**
     * Lists all invisible (=newly imported) records
     *
     * @Route("/{visible}", 
     *        name="admin_record", 
     *        requirements={"visible"="(new|visible)"})
     */
    public function indexAction(Request $request, $visible = 'new')
    {
        $securityContext = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $user = null;
        if (!$securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $em->getRepository('SeleyaBundle:User')
                       ->getUser($securityContext->getToken()->getUser()->getUsername());
        }
        
        $recordQuery = $em->getRepository('SeleyaBundle:Record')
                          ->getQueryForAllRecords($visible == 'visible', $user);
                          
        // @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/124
        if (!$request->get('sort') && !$request->get('direction')) {
            $_GET['sort'] = 'r.title';
            $_GET['direction'] = 'asc';
        }
                          
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $recordQuery,
            $request->get('page', 1),
            RecordController::RECORDS_PER_PAGE
        );
        
        // @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/124
        if (!$request->get('sort') && !$request->get('direction')) {
            $pagination->setParam('sort', 'r.title');
            $pagination->setParam('direction', 'asc');
        }
        
        $title = ($visible == 'visible') ?
            $this->get('translator')->trans('Sichtbare Aufzeichnungen') :
            $this->get('translator')->trans('Neue Aufzeichnungen');

        return $this->render(
            'SeleyaBundle:Admin:Record/index.html.twig',
            array(
                'title'   => $title,
                'pagination' => $pagination
            )
        );
    }

    /**
     * Creates a new record
     * Shows the form first and if method is POST
     * stores the record
     * 
     * @param Request $request
     * 
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/new", name="admin_record_new")
     */
    public function newAction(Request $request)
    {
        $record = new Record();
        $metadata = array();
        $metadataConfigs = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('SeleyaBundle:MetadataConfig')
                                ->findAllOrdered();
        foreach ($metadataConfigs as $config) {
            $meta = new Metadata();
            $meta->setConfig($config);
            $meta->setRecord($record);
            $metadata[] = $meta;
            $record->getMetadata()->add($meta);
        }
        
        $form = $this->createForm(new RecordType($this->get('translator'), $metadata), $record);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                foreach ($form as $key => $value) {
                    if (substr($key, 0, 9) !== 'metadata_') {
                        continue;
                    }
                    $metadataId = substr($key, 9);
                    foreach ($metadata as $meta) {
                        if ($meta->getConfig()->getId() == $metadataId) {
                            if ($value->getData() !== null) {
                                if ($meta->getConfig()->getDefinition()->getId() == 'select') {
                                    $option = $this->getDoctrine()
                                                   ->getManager()
                                                   ->getRepository('SeleyaBundle:MetadataConfigOption')
                                                   ->findOneById($value->getData());
                                    if ($option !== null) {
                                        $meta->setValue($option);
                                    }
                                } else {
                                    $meta->setValue($value->getData());
                                }
                            }
                            break;
                        }
                    }
                }
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($record);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Aufzeichnung wurde hinzugefügt')
                );
                return $this->redirect($this->generateUrl('admin_record'));
            }
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Record/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Updates a record and its metadata
     * Shows the form at first and if the method is POST
     * updates the record
     * 
     * @param Request $request The request object
     * @param int     $id      Id of the record to update
     * 
     * @Route("/update/{id}", name="admin_record_update")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->getRecord($id);
        if (!$record) {
            throw $this->createNotFoundException('Unable to find record.');
        }

        if (!$this->currentUserCanEditRecord($record)) {
            throw new AccessDeniedHttpException('You are not allowed to edit this record.');
        }

        $metadataConfigs = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('SeleyaBundle:MetadataConfig')
                                ->findAllOrdered();
        foreach ($metadataConfigs as $config) {
            $metadataExists = false;
            foreach ($record->getMetadata() as $metadata) {
                if ($metadata->getConfig()->getId() == $config->getId()) {
                    $metadataExists = true;
                    break;
                }
            }
            if ($metadataExists) {
                continue;
            }
            $metadata = new Metadata();
            $metadata->setConfig($config);
            $metadata->setRecord($record);
            $record->getMetadata()->add($metadata);
        }
        
        $recordWasVisible = $record->isVisible();
        
        $form = $this->createForm(new RecordType($this->get('translator'), $record->getMetadata()), $record);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                foreach ($form as $key => $value) {
                    if (substr($key, 0, 9) !== 'metadata_') {
                        continue;
                    }
                    $metadataId = substr($key, 9);
                    foreach ($record->getMetadata() as $meta) {
                        if ($meta->getConfig()->getId() == $metadataId) {
                            if ($value->getData() !== null) {
                                if ($meta->getConfig()->getDefinition()->getId() == 'select') {
                                    $option = $this->getDoctrine()
                                                   ->getManager()
                                                   ->getRepository('SeleyaBundle:MetadataConfigOption')
                                                   ->findOneById($value->getData());
                                    if ($option !== null) {
                                        $meta->setValue($option);
                                    }
                                } else {
                                    $meta->setValue($value->getData());
                                }
                            }
                            break;
                        }
                    }
                }

                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Aufzeichnung wurde aktualisiert')
                );
                return $this->redirect(
                    $this->generateUrl('admin_record', array('visible' => ($recordWasVisible) ? 'visible' : 'new'))
                );
            }
        }
                
        return $this->render(
            'SeleyaBundle:Admin:Record/update.html.twig',
            array(
                'form' => $form->createView(),
                'id'   => $record->getId()
            )
        );
    }

    /**
     * Deletes a record
     * Shows a confirmation template and if method is POST deletes the record
     * 
     * @param Request $request The request object
     * @param int     $id      Id of record to delete
     * @todo delete preview image
     * @todo delete record in matterhorn (with optional checkbox in form)
     * 
     * @Route("/delete/{id}", name="admin_record_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->findOneById($id);
        if (!$record) {
            throw $this->createNotFoundException('Unable to find record.');
        }
        
        if (!$this->currentUserCanEditRecord($record)) {
            throw new AccessDeniedHttpException('You are not allowed to edit this record.');
        }
        
        if ($request->isMethod('POST') && $request->request->get('confirmed') == 1) {
            $recordWasVisible = $record->isVisible();
            $em->remove($record);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Aufzeichnung wurde gelöscht')
            );
            return $this->redirect(
                $this->generateUrl('admin_record', array('visible' => ($recordWasVisible) ? 'visible' : 'new'))
            );
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Record/delete.html.twig',
            array('record' => $record)
        );
    }

    /**
     * Retrieves all episodes from Matterhorn,
     * filters out new episodes and imports them (if method is POST)
     * 
     * @param Request $request The request object
     * 
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/import", name="admin_record_import")
     */
    public function importAction(Request $request)
    {
        $matterhorn = $this->get('seleya.matterhorn');
        $episodes = $matterhorn->getEpisodes();
        
        $newEpisodes = array();
        $repository = $this->getDoctrine()
                           ->getRepository('SeleyaBundle:Record');
        foreach ($episodes as $episode) {
            $record = $repository->findOneByExternalId($episode['id']);
            if ($record === null) {
                $newEpisodes[] = $episode;
            }
        }
        
        if ($request->isMethod('POST')) {
            $numImported = 0;
            $em = $this->getDoctrine()->getManager();
            foreach ($newEpisodes as $episode) {
                if ($request->request->get('record_' . $episode['id']) == 1) {
                    $record = new Record();
                    $record->setTitle($episode['title']);
                    $record->setExternalId($episode['id']);
                    $record->setRecordDate($episode['created']);
                    if ($episode['preview'] !== null) {
                        $record->downloadPreviewImage($episode['preview']);
                    }
                    $em->persist($record);
                    $numImported++;
                }
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans(
                    '%number% Aufzeichnung(en) wurden importiert',
                    array('%number%' => $numImported)
                )
            );
            return $this->redirect($this->generateUrl('admin_record'));
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Record/import.html.twig',
            array('records' => $newEpisodes)
        );
    }

    /**
     * Checks if the current user is allowed to edit/delete a record
     * 
     * @param Record $record Record to check
     * @return boolean
     */
    private function currentUserCanEditRecord($record)
    {
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $user = $this->getDoctrine()->getManager()->getRepository('SeleyaBundle:User')
                   ->getUser($securityContext->getToken()->getUser()->getUsername());
                   
        foreach ($record->getLecturers() as $lecturer) {
            if ($lecturer->getId() == $user->getId()) {
                return true;
            }
        }
        
        foreach ($record->getUsers() as $rUser) {
            if ($rUser->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
