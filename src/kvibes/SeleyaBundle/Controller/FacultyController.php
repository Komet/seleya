<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Faculty;
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
    const COURSES_PER_PAGE = 20;
    
    /**
     * @Route("/", name="faculties")
     */
    public function indexAction()
    {
        $faculties = $this->getDoctrine()->getManager()
                          ->getRepository('SeleyaBundle:Faculty')
                          ->getFacultiesWithRecords();
                          
        return $this->render(
            'SeleyaBundle:Faculty:index.html.twig',
            array('faculties' => $faculties)
        );
    }
    
    /**
     * @Route("/{facultyId}/{page}", 
     *        name="faculty", 
     *        requirements={"page"="\d+"})
     */
    public function facultyAction($facultyId, $page = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $faculty = $em->getRepository('SeleyaBundle:Faculty')
                      ->findOneById($facultyId);
                        
        if ($faculty === null) {
            throw $this->createNotFoundException('Fachbereich wurde nicht gefunden.');
        }

        $courses         = $this->coursesInFacultyAction($facultyId, $page);
        $numberOfCourses = $em->getRepository('SeleyaBundle:Course')
                              ->getCourseCountForFaculty($facultyId);
        $hasMoreCourses  = ($page*FacultyController::COURSES_PER_PAGE+count($courses)) < $numberOfCourses;
        
        return $this->render(
            'SeleyaBundle:Faculty:faculty.html.twig',
            array(
                'faculty'        => $faculty,
                'courses'        => $courses,
                'currentPage'    => $page,
                'hasMoreCourses' => $hasMoreCourses
            )
        );
    }

    /**
     * @Route("/courses/{facultyId}/{page}/{render}", 
     *        name="faculty_courses", 
     *        options={"expose"=true},
     *        requirements={"facultyId"="\d+", "page"="\d+"})
     */
    public function coursesInFacultyAction($facultyId, $page, $render = false)
    {
        $em = $this->getDoctrine()->getManager();
        $courses = $em->getRepository('SeleyaBundle:Course')
                      ->getCoursesInFaculty(
                          $facultyId,
                          FacultyController::COURSES_PER_PAGE,
                          $page*FacultyController::COURSES_PER_PAGE
                      );
                
        if (!$render) {
            return $courses;
        }
        
        $numberOfCourses = $em->getRepository('SeleyaBundle:Course')
                              ->getCourseCountForFaculty($facultyId);
        $hasMoreCourses  = ($page*FacultyController::COURSES_PER_PAGE+count($courses)) < $numberOfCourses;
        $htmlContents = array();
        foreach ($courses as $course) {
            $htmlContents[] = $this->renderView(
                'SeleyaBundle:Faculty:course_entry.html.twig',
                array('course' => $course)
            );
        }
        
        return new Response(
            json_encode(
                array(
                    'html'           => $htmlContents,
                    'hasMoreCourses' => $hasMoreCourses
                )
            ),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/courses/lastRecord/{facultyId}", 
     *        name="faculty_courses_lastRecord",
     *        options={"expose"=true},
     *        requirements={"page"="\d+"})
     */
    public function coursesInFacultyOrderedByRecordDate($facultyId)
    {
        $em = $this->getDoctrine()->getManager();
        $courses = $em->getRepository('SeleyaBundle:Course')
                      ->getCoursesInFacultyOrderedByRecordDate($facultyId, FacultyController::COURSES_PER_PAGE, 0);
        $htmlContents = array();
        foreach ($courses as $course) {
            $htmlContents[] = $this->renderView(
                'SeleyaBundle:Faculty:course_entry_small.html.twig',
                array('course' => $course)
            );
        }
        
        return new Response(
            json_encode(
                array(
                    'html' => $htmlContents
                )
            ),
            200,
            array('Content-Type' => 'application/json')
        );
    }
}
