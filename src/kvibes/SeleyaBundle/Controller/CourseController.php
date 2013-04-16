<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Course;
use kvibes\SeleyaBundle\Entity\Record;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/course")
 */
class CourseController extends Controller
{
    const RECORDS_PER_PAGE = 20;
    
    /**
     * @Route("/{courseId}/{page}/{sortOrder}/{sortDirection}", 
     *        name="course", 
     *        requirements={"page"="\d+", "sortOrder"="(title|recordDate)", "sortDirection"="(ASC|DESC)"})
     */
    public function indexAction($courseId, $page = 0, $sortOrder = 'recordDate', $sortDirection = 'DESC')
    {
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('SeleyaBundle:Course')
                     ->findOneById($courseId);
                        
        if ($course === null) {
            throw $this->createNotFoundException('Veranstaltung wurde nicht gefunden.');
        }
                        
        $records         = $this->recordsInCourseAction($courseId, $page, $sortOrder, $sortDirection);
        $numberOfRecords = $em->getRepository('SeleyaBundle:Record')
                              ->getRecordCountForCourse($courseId);
        $hasMoreRecords  = ($page*CourseController::RECORDS_PER_PAGE+count($records)) < $numberOfRecords;
        
        $sortOrders = array(
            'title'      => 'Titel',
            'recordDate' => 'Datum'
        );
        
        return $this->render(
            'SeleyaBundle:Course:course.html.twig',
            array(
                'course'               => $course,
                'records'              => $records,
                'sortOrders'           => $sortOrders,
                'currentPage'          => $page,
                'currentSortOrder'     => $sortOrder,
                'currentSortDirection' => $sortDirection,
                'hasMoreRecords'       => $hasMoreRecords
            )
        );
    }

    /**
     * @Route("/{courseId}/records/{page}/{sortOrder}/{sortDirection}/{render}", 
     *        name="course_records", 
     *        options={"expose"=true},
     *        requirements={"page"="\d+", "sortOrder"="(title|recordDate)", "sortDirection"="(ASC|DESC)"})
     */
    public function recordsInCourseAction($courseId, $page, $sortOrder, $sortDirection, $render = false)
    {
        $em      = $this->getDoctrine()->getManager();
        $records = $em->getRepository('SeleyaBundle:Record')
                      ->findBy(
                          array('course' => $courseId, 'visible' => 1),
                          array($sortOrder => $sortDirection),
                          CourseController::RECORDS_PER_PAGE,
                          $page*CourseController::RECORDS_PER_PAGE
                      );

        if (!$render) {
            return $records;
        }
        
        $numberOfRecords = $em->getRepository('SeleyaBundle:Record')
                              ->getRecordCountForCourse($courseId);
        $hasMoreRecords  = ($page*CourseController::RECORDS_PER_PAGE+count($records)) < $numberOfRecords;
        $htmlContents = array();
        foreach ($records as $record) {
            $htmlContents[] = $this->renderView(
                'SeleyaBundle:Course:record_entry.html.twig',
                array('record' => $record)
            );
        }
        
        return new Response(
            json_encode(
                array(
                    'html'           => $htmlContents,
                    'hasMoreRecords' => $hasMoreRecords
                )
            ),
            200,
            array('Content-Type' => 'application/json')
        );
    }
}
