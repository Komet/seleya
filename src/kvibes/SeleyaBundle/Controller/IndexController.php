<?php

namespace kvibes\SeleyaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="index")
     */
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
