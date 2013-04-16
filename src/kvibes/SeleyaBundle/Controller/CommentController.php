<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Comment;
use kvibes\SeleyaBundle\Entity\Record;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    const COMMENTS_PER_PAGE = 10;
    
    /**
     * Adds a comment from the current user to a record
     *  
     * @param int $recordId ID of the record
     * @return JSON response
     * 
     * @Secure(roles="ROLE_USER")
     * @Route("/add/{recordId}", name="comment_add", options={"expose"=true})
     */
    public function addAction(Request $request, $recordId)
    {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $em->getRepository('SeleyaBundle:User')
                   ->getUser($securityContext->getToken()->getUser()->getUsername());
        $record = $em->getRepository('SeleyaBundle:Record')->findOneById($recordId);
        if ($record === null || $user === null) {
            return new Response('', 400, array('Content-Type'=>'application/json'));
        }

        $commentText = $request->get('comment');
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setRecord($record);
        $comment->setText($commentText);
        $record->getComments()->add($comment);
        $em->flush();
        
        $htmlContent = $this->renderView(
            'SeleyaBundle:Record:comment_entry.html.twig',
            array('comment' => $comment)
        );
        
        return new Response(
            json_encode(
                array(
                    'success'   => true,
                    'html'      => $htmlContent,
                    'commentId' => $comment->getId()
                )
            ),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Returns a JSON response with comments
     * 
     * @param Request $request  The Request
     * @param int     $recordId The Record ID
     * @param int     $page     The page number
     * 
     * @return JSON Response
     * 
     * @Route("/get/{recordId}/{page}", name="comments", options={"expose"=true})
     */
    public function getAction(Request $request, $recordId, $page)
    {
        $commentsToExclude = $request->get('commentsToExclude');
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('SeleyaBundle:Comment')
                       ->getCommentsForRecord(
                           $recordId,
                           $page,
                           CommentController::COMMENTS_PER_PAGE,
                           $commentsToExclude
                       );
        $numberOfComments = $em->getRepository('SeleyaBundle:Comment')
                               ->getCommentsCountForRecord($recordId);
        $hasMoreComments = ($page*CommentController::COMMENTS_PER_PAGE+count($comments)) < $numberOfComments;
        $htmlContents = array();
        foreach ($comments as $comment) {
            $htmlContents[] = $this->renderView(
                'SeleyaBundle:Record:comment_entry.html.twig',
                array('comment' => $comment)
            );
        }
        
        return new Response(
            json_encode(
                array(
                    'html'            => $htmlContents,
                    'hasMoreComments' => $hasMoreComments
                )
            ),
            200,
            array('Content-Type' => 'application/json')
        );
    }
}
