<?php

namespace kvibes\SeleyaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecordController extends Controller
{
    public function indexAction($id)
    {
        $record = $this->getDoctrine()->getManager()
                       ->getRepository('SeleyaBundle:Record')
                       ->findOneById($id);
        if ($record === null) {
            throw $this->createNotFoundException('Unable to find record.');
        }                        

        return $this->render('SeleyaBundle:Record:index.html.twig', array(
            'record' => $record
        ));
    }
}
