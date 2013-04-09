<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Bookmark;
use kvibes\SeleyaBundle\Controller\CommentController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        $securityContext = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $bookmark = null;
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->getRecord($id);
        $comments = $em->getRepository('SeleyaBundle:Comment')
                       ->getCommentsForRecord($id, 0, CommentController::commentsPerPage);
        $numberOfComments = $em->getRepository('SeleyaBundle:Comment')
                               ->getCommentsCountForRecord($id);

        if ($record === null) {
            throw $this->createNotFoundException('Unable to find record.');
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
            'hasBookmark'     => $bookmark !== null
        ));
    }
}
