<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Model\RecordStats;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/")
 */
class IndexController extends Controller
{
    const NUMBER_OF_HOT_RECORDS = 10;
    const NUMBER_OF_NEW_RECORDS = 10;
    const INTERVAL_OF_HOT_RECORDS = 'P7D';
    
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository('SeleyaBundle:Record')
                      ->findLatest(IndexController::NUMBER_OF_NEW_RECORDS);
                      
        $recordStats = new RecordStats($this->getDoctrine());
        $hotRecords  = $recordStats->getHotRecords(
            new \DateInterval(IndexController::INTERVAL_OF_HOT_RECORDS),
            IndexController::NUMBER_OF_HOT_RECORDS
        );
                        
        return $this->render(
            'SeleyaBundle:Index:index.html.twig',
            array(
                'records'    => $records,
                'hotRecords' => $hotRecords
            )
        );
    }
}
