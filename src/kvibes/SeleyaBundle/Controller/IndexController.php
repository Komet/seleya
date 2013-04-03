<?php

namespace kvibes\SeleyaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $records = $this->getDoctrine()->getManager()
                        ->getRepository('SeleyaBundle:Record')
                        ->findLatest(10);

        return $this->render('SeleyaBundle:Index:index.html.twig', array(
            'records' => $records
        ));
    }
}
