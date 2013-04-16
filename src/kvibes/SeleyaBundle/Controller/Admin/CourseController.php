<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use kvibes\SeleyaBundle\Entity\Course;
use kvibes\SeleyaBundle\Form\Type\CourseType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/course")
 */
class CourseController extends Controller
{
    const COURSES_PER_PAGE = 20;
    
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/", name="admin_course")
     */ 
    public function indexAction(Request $request)
    {
        $coursesQuery = $this->getDoctrine()
                             ->getManager()
                             ->getRepository('SeleyaBundle:Course')
                             ->getAllCoursesQuery();                        

        // @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/124
        if (!$request->get('sort') && !$request->get('direction')) {
            $_GET['sort'] = 'c.name';
            $_GET['direction'] = 'asc';
        }
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($coursesQuery, $request->get('page', 1), CourseController::COURSES_PER_PAGE);   
        
        // @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/124
        if (!$request->get('sort') && !$request->get('direction')) {
            $pagination->setParam('sort', 'c.name');
            $pagination->setParam('direction', 'asc');
        }
        
        return $this->render('SeleyaBundle:Admin:Course/index.html.twig', array(
            'pagination' => $pagination
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/new", name="admin_course_new")
     */ 
    public function newAction(Request $request)
    {
        $course = new Course();
        
        $form = $this->createForm(new CourseType($this->get('translator')), $course);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Veranstaltung wurde hinzugefügt'));                
                return $this->redirect($this->generateUrl('admin_course'));
            }
        }
        
        return $this->render('SeleyaBundle:Admin:Course/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/update/{id}", name="admin_course_update")
     */ 
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('SeleyaBundle:Course')
                     ->findOneById($id);
        if (!$course) {
            throw $this->createNotFoundException('Unable to find course.');
        }
        
        $form = $this->createForm(new CourseType($this->get('translator')), $course);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Veranstaltung wurde aktualisiert'));      
                return $this->redirect($this->generateUrl('admin_course'));
            }
        }
        
        return $this->render('SeleyaBundle:Admin:Course/update.html.twig', array(
            'form' => $form->createView(),
            'id'   => $course->getId()
        ));
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Route("/delete/{id}", name="admin_course_delete")
     */ 
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('SeleyaBundle:Course')
                     ->findOneById($id);
        if (!$course) {
            throw $this->createNotFoundException('Unable to find course.');
        }
        
        if ($request->isMethod('POST') && $request->request->get('confirmed') == 1) {
            $em->remove($course);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('Veranstaltung wurde gelöscht'));                
            return $this->redirect($this->generateUrl('admin_course'));
        }
        
        return $this->render('SeleyaBundle:Admin:Course/delete.html.twig', array(
            'course' => $course
        ));
    }
}
