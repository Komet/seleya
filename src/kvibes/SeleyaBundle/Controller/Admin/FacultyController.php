<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use kvibes\SeleyaBundle\Entity\Faculty;
use kvibes\SeleyaBundle\Form\Type\FacultyType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/faculty")
 */
class FacultyController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/", name="admin_faculty")
     */
    public function indexAction()
    {
        $faculties = $this->getDoctrine()->getManager()
                          ->getRepository('SeleyaBundle:Faculty')
                          ->findBy(array(), array('name' => 'ASC'));
        return $this->render(
            'SeleyaBundle:Admin:Faculty/index.html.twig',
            array('faculties' => $faculties)
        );
    }
    
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/new", name="admin_faculty_new")
     */
    public function newAction(Request $request)
    {
        $faculty = new Faculty();
        
        $form = $this->createForm(new FacultyType($this->get('translator')), $faculty);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($faculty);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Fachbereich wurde hinzugefügt')
                );
                return $this->redirect($this->generateUrl('admin_faculty'));
            }
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Faculty/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/update/{id}", name="admin_faculty_update")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $faculty = $em->getRepository('SeleyaBundle:Faculty')
                      ->findOneById($id);
        if (!$faculty) {
            throw $this->createNotFoundException('Unable to find faculty.');
        }
        
        $form = $this->createForm(new FacultyType($this->get('translator')), $faculty);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Fachbereich wurde aktualisiert')
                );
                return $this->redirect($this->generateUrl('admin_faculty'));
            }
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Faculty/update.html.twig',
            array(
                'form' => $form->createView(),
                'id'   => $faculty->getId()
            )
        );
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/delete/{id}", name="admin_faculty_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $faculty = $em->getRepository('SeleyaBundle:Faculty')
                      ->findOneById($id);
        if (!$faculty) {
            throw $this->createNotFoundException('Unable to find faculty.');
        }
        
        if ($request->isMethod('POST') && $request->request->get('confirmed') == 1) {
            $em->remove($faculty);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Fachbereich wurde gelöscht')
            );
            return $this->redirect($this->generateUrl('admin_faculty'));
        }
        
        return $this->render(
            'SeleyaBundle:Admin:Faculty/delete.html.twig',
            array('faculty' => $faculty)
        );
    }
}
