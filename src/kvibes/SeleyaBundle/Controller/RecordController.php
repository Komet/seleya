<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Bookmark;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/record")
 */
class RecordController extends Controller
{
    const RECENT_RECORDS = 5;
    
    /**
     * Shows a record
     *  
     * @param int $id ID of the record
     * @return Rendered template
     * 
     * @Route("/{id}", name="record")
     */
    public function indexAction($id)
    {
        $securityContext = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $bookmark = null;
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->getRecord($id);
        $recentRecordsInCourse = $em->getRepository('SeleyaBundle:Record')
                                    ->getRecentRecordsInCourse($record->getCourse()->getId(), $record->getId(), RecordController::RECENT_RECORDS);
        $comments = $em->getRepository('SeleyaBundle:Comment')
                       ->getCommentsForRecord($id, 0, CommentController::COMMENTS_PER_PAGE);
        $numberOfComments = $em->getRepository('SeleyaBundle:Comment')
                               ->getCommentsCountForRecord($id);

        if ($record === null) {
            throw $this->createNotFoundException('Aufzeichnung wurde nicht gefunden.');
        }

        if ($securityContext->isGranted('ROLE_USER')) {
            $user = $em->getRepository('SeleyaBundle:User')
                       ->getUser($securityContext->getToken()->getUser()->getUsername());
    
            $bookmark = $em->getRepository('SeleyaBundle:Bookmark')
                           ->getBookmarkForRecord($record, $user);
        }

        return $this->render('SeleyaBundle:Record:index.html.twig', array(
            'record'          => $record,
            'comments'        => $comments,
            'hasMoreComments' => count($comments) < $numberOfComments,
            'hasBookmark'     => $bookmark !== null,
            'recentRecords'   => $recentRecordsInCourse
        ));
    }
}
