<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use kvibes\SeleyaBundle\Entity\Metadata;
use kvibes\SeleyaBundle\Entity\Record;
use kvibes\SeleyaBundle\Form\Type\RecordType;

/**
 * The RecordController
 *
 * @package SeleyaBundle
 * @author  Daniel Kabel <daniel.kabel@me.com>
 * @version 1.0
 */
class RecordController extends Controller
{
    /**
     * Lists all invisible (=newly imported) records
     *
     * @todo check if superadmin, show, otherwise show records of user
     */
    public function indexAction()
    {
        $records = $this->getDoctrine()->getManager()
                        ->getRepository('SeleyaBundle:Record')
                        ->findNewOrderedByCreated();

        return $this->render('SeleyaBundle:Admin:Record/index.html.twig', array(
            'records' => $records
        ));
    }
    
    /**
     * Lists all visible records
     *
     * @todo check if superadmin, show, otherwise show records of user
     */
    public function listVisibleAction()
    {
        $records = $this->getDoctrine()->getManager()
                        ->getRepository('SeleyaBundle:Record')
                        ->findVisibleOrderedByCreated();

        return $this->render('SeleyaBundle:Admin:Record/index.html.twig', array(
            'records' => $records
        ));
    }

    /**
     * Creates a new record
     * Shows the form first and if method is POST
     * stores the record
     * 
     * @param Request $request
     * 
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
        
        $form = $this->createForm(new RecordType($metadata), $record);
        
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
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Aufzeichnung wurde hinzugefügt'));                
                return $this->redirect($this->generateUrl('admin_record'));
            }
        }
        
        return $this->render('SeleyaBundle:Admin:Record/new.html.twig', array(
            'form' => $form->createView(),
        ));        
    }

    /**
     * Updates a record and its metadata
     * Shows the form at first and if the method is POST
     * updates the record
     * 
     * @param Request $request The request object
     * @param int     $id      Id of the record to update
     * @todo check if superadmin or user owns record
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->findOneById($id);
        if (!$record) {
            throw $this->createNotFoundException('Unable to find record.');
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
        
        $form = $this->createForm(new RecordType($record->getMetadata()), $record);
        
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
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Aufzeichnung wurde aktualisiert'));      
                return $this->redirect($this->generateUrl('admin_record'));
            }
        }
                
        return $this->render('SeleyaBundle:Admin:Record/update.html.twig', array(
            'form' => $form->createView(),
            'id'   => $record->getId()
        ));        
    }

    /**
     * Deletes a record
     * Shows a confirmation template and if method is POST deletes the record
     * 
     * @param Request $request The request object
     * @param int     $id      Id of record to delete
     * @todo check if superadmin or user owns record
     * @todo delete preview image
     * @todo delete record in matterhorn (with optional checkbox in form)
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->findOneById($id);
        if (!$record) {
            throw $this->createNotFoundException('Unable to find record.');
        }
        
        if ($request->isMethod('POST') && $request->request->get('confirmed') == 1) {
            $em->remove($record);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Aufzeichnung wurde gelöscht'));                
            return $this->redirect($this->generateUrl('admin_record'));
        }
        
        return $this->render('SeleyaBundle:Admin:Record/delete.html.twig', array(
            'record' => $record
        ));
    }

    /**
     * Retrieves all episodes from Matterhorn,
     * filters out new episodes and imports them (if method is POST)
     * 
     * @param Request $request The request object
     * 
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
        
        return $this->render('SeleyaBundle:Admin:Record/import.html.twig', array(
            'records' => $newEpisodes
        ));
    }
}