<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Comment;
use kvibes\SeleyaBundle\Entity\Record;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/faculty")
 */
class FacultyController extends Controller
{
    const RECORDS_PER_PAGE = 20;
    
    /**
     * @Route("/", name="faculties")
     */
    public function indexAction()
    {
        $faculties = $this->getDoctrine()->getManager()
                          ->getRepository('SeleyaBundle:Faculty')
                          ->getFacultiesWithRecords();
        return $this->render('SeleyaBundle:Faculty:index.html.twig', array(
            'faculties' => $faculties
        ));
    }
    
    /**
     * @Route("/{id}/{page}/{sortOrder}/{sortDirection}", 
     *        name="faculty", 
     *        requirements={"page"="\d+", "sortOrder"="(title|recordDate)", "sortDirection"="(ASC|DESC)"})
     */
    public function facultyAction($id, $page = 0, $sortOrder = 'recordDate', $sortDirection = 'DESC')
    {
        $em = $this->getDoctrine()->getManager();
        $faculty = $em->getRepository('SeleyaBundle:Faculty')
                        ->findOneById($id);
                        
        if ($faculty === null) {
            throw $this->createNotFoundException('Fachbereich wurde nicht gefunden.');
        }
                        
        $records         = $this->recordsInFacultyAction($id, $page, $sortOrder, $sortDirection);
        $numberOfRecords = $em->getRepository('SeleyaBundle:Record')
                              ->getRecordCountForFaculty($id);
        $hasMoreRecords  = ($page*FacultyController::RECORDS_PER_PAGE+count($records)) < $numberOfRecords;
        
        $sortOrders = array(
            'title'      => 'Titel',
            'recordDate' => 'Datum'
        );
        
        return $this->render('SeleyaBundle:Faculty:faculty.html.twig', array(
            'faculty'              => $faculty,
            'records'              => $records,
            'sortOrders'           => $sortOrders,
            'currentPage'          => $page,
            'currentSortOrder'     => $sortOrder,
            'currentSortDirection' => $sortDirection,
            'hasMoreRecords'       => $hasMoreRecords
        ));
    }

    /**
     * @Route("/records/{id}/{page}/{sortOrder}/{sortDirection}/{render}", 
     *        name="faculty_records", 
     *        options={"expose"=true},
     *        requirements={"page"="\d+", "sortOrder"="(title|recordDate)", "sortDirection"="(ASC|DESC)"})
     */
    public function recordsInFacultyAction($id, $page, $sortOrder, $sortDirection, $render = false)
    {
        $em      = $this->getDoctrine()->getManager();
        $records = $em->getRepository('SeleyaBundle:Record')
                      ->findBy(
                          array('faculty' => $id, 'visible' => 1), 
                          array($sortOrder => $sortDirection),
                          FacultyController::RECORDS_PER_PAGE,
                          $page*FacultyController::RECORDS_PER_PAGE
                      );

        if (!$render) {
            return $records;
        }
        
        $numberOfRecords = $em->getRepository('SeleyaBundle:Record')
                              ->getRecordCountForFaculty($id);
        $hasMoreRecords  = ($page*FacultyController::RECORDS_PER_PAGE+count($records)) < $numberOfRecords;
        $htmlContents = array();
        foreach ($records as $record) {
            $htmlContents[] = $this->renderView('SeleyaBundle:Faculty:record_entry.html.twig', array(
                'record' => $record
            ));
        }
        
        return new Response(
            json_encode(
                array(
                    'html'           => $htmlContents,
                    'hasMoreRecords' => $hasMoreRecords
                )
            ), 
            200, 
            array(
                'Content-Type' => 'application/json'
            )
        );
    }
}
