<?php

namespace kvibes\SeleyaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecordController extends Controller
{
    /**
     * Shows a record
     *  
     * @param int $id ID of the record
     * @return Rendered template
     */
    public function indexAction($id)
    {
        $record = $this->getDoctrine()->getManager()
                       ->getRepository('SeleyaBundle:Record')
                       ->getRecord($id);
        if ($record === null) {
            throw $this->createNotFoundException('Unable to find record.');
        }

        return $this->render('SeleyaBundle:Record:index.html.twig', array(
            'record' => $record
        ));
    }
}
