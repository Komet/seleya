<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin")
     */
    public function indexAction()
    {
        $securityContext = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $user = null;
        $username = 'Super-Admin';
        $isLecturer = false;
        $myRecordsPagination = array();
        if (!$securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $em->getRepository('SeleyaBundle:User')
                       ->getUser($securityContext->getToken()->getUser()->getUsername());
            $username = $user->getCommonName();
            $isLecturer = true;
            $myRecordsQuery = $em->getRepository('SeleyaBundle:Record')
                                 ->getQueryForAllRecords(true, $user);
            $paginator = $this->get('knp_paginator');
            $myRecordsPagination = $paginator->paginate($myRecordsQuery, 1, 5);
        }
        
        $newRecordsQuery = $em->getRepository('SeleyaBundle:Record')
                              ->getQueryForAllRecords(false, $user);
        $newRecords = $newRecordsQuery->getResult();
        
        return $this->render(
            'SeleyaBundle:Admin:index.html.twig',
            array(
                'newRecords' => $newRecords,
                'username'   => $username,
                'isLecturer' => $isLecturer,
                'myRecords'  => $myRecordsPagination
            )
        );
    }
}
