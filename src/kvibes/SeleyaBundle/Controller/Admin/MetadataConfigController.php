<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use kvibes\SeleyaBundle\Entity\MetadataConfig;
use kvibes\SeleyaBundle\Entity\MetadataConfigOption;
use kvibes\SeleyaBundle\Form\Type\MetadataConfigType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/metadataConfig")
 */
class MetadataConfigController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/", name="admin_metadataConfig")
     */ 
    public function indexAction()
    {
        $metadataConfigs = $this->getDoctrine()->getManager()
                                ->getRepository('SeleyaBundle:MetadataConfig')
                                ->findAllOrdered();
        return $this->render('SeleyaBundle:Admin:MetadataConfig/index.html.twig', array(
            'metadataConfigs' => $metadataConfigs
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/new", name="admin_metadataConfig_new")
     */ 
    public function newAction(Request $request)
    {
        $metadataConfig = new MetadataConfig();
        
        $form = $this->createForm(new MetadataConfigType($this->get('translator')), $metadataConfig);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($metadataConfig->getDefinition()->getId() != 'select') {
                    $metadataConfig->setOptions(array());
                }
                $em = $this->getDoctrine()->getManager();
                $metadataConfig->setDisplayOrder($em->getRepository('SeleyaBundle:MetadataConfig')->getNextDisplayOrder());
                $em->persist($metadataConfig);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Metadatum wurde hinzugefügt'));                
                return $this->redirect($this->generateUrl('admin_metadataConfig'));
            }
        }
        
        return $this->render('SeleyaBundle:Admin:MetadataConfig/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/update/{id}", name="admin_metadataConfig_update")
     */ 
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $metadataConfig = $em->getRepository('SeleyaBundle:MetadataConfig')
                             ->findOneById($id);
        if (!$metadataConfig) {
            throw $this->createNotFoundException('Unable to find metadata.');
        }
        
        $originalOptions = array();
        foreach ($metadataConfig->getOptions() as $option) {
            $originalOptions[] = $option;
        }
        
        $form = $this->createForm(new MetadataConfigType($this->get('translator')), $metadataConfig);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                foreach ($metadataConfig->getOptions() as $option) {
                    foreach ($originalOptions as $key => $toDel) {
                        if ($toDel->getId() === $option->getId()) {
                            unset($originalOptions[$key]);
                        }
                    }
                }
                
                foreach ($originalOptions as $option) {
                    $metadataConfig->getOptions()->removeElement($option);
                    $em->remove($option);
                }
                
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Metadatum wurde aktualisiert'));      
                return $this->redirect($this->generateUrl('admin_metadataConfig'));
            }
        }
        
        return $this->render('SeleyaBundle:Admin:MetadataConfig/update.html.twig', array(
            'form' => $form->createView(),
            'id'   => $metadataConfig->getId()
        ));
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/delete/{id}", name="admin_metadataConfig_delete")
     */ 
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $metadataConfig = $em->getRepository('SeleyaBundle:MetadataConfig')
                             ->findOneById($id);
        if (!$metadataConfig) {
            throw $this->createNotFoundException('Unable to find metadata.');
        }
        
        if ($request->isMethod('POST') && $request->request->get('confirmed') == 1) {
            $metadatas = $em->createQueryBuilder()
                            ->select(array('m'))
                            ->from('SeleyaBundle:Metadata', 'm')
                            ->where('m.config=:mco')
                            ->setParameter('mco', $metadataConfig)
                            ->getQuery()
                            ->getResult();
            foreach ($metadatas as $metadata) {
                $em->remove($metadata);
            }                                     
            
            $em->remove($metadataConfig);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Metadatum wurde gelöscht'));                
            return $this->redirect($this->generateUrl('admin_metadataConfig'));
        }
        
        return $this->render('SeleyaBundle:Admin:MetadataConfig/delete.html.twig', array(
            'metadata' => $metadataConfig
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/order", name="admin_metadataConfig_order", options={"expose"=true})
     * @Method({"POST"})
     */ 
    public function orderAction(Request $request)
    {
        $order = $request->get('order');
        if ($order === null) {
            return new Response('', 400, array('Content-Type'=>'application/json'));
        }

        $em = $this->getDoctrine()->getManager();
        $count = 0;
        foreach (json_decode($order) as $id) {
            $metadata = $em->getRepository('SeleyaBundle:MetadataConfig')
                           ->findOneById(substr($id, 5));
            if ($metadata) {
                $metadata->setDisplayOrder($count);
            }
            $count++;
        }
        $em->flush();
        
        return new Response(json_encode(array('success' => true)), 200, array('Content-Type'=>'application/json'));
    }
}
