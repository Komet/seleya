<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Bookmark;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/bookmark")
 */
class BookmarkController extends Controller
{
    /**
     * Shows all bookmarks for the current user
     *  
     * @return Rendered template
     * 
     * @Secure(roles="ROLE_USER")
     * @Route("/", name="bookmarks")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $em->getRepository('SeleyaBundle:User')
                   ->getUser($securityContext->getToken()->getUser()->getUsername());

        $bookmarks = $em->getRepository('SeleyaBundle:Bookmark')
                        ->getBookmarksForUser($user);
        
        return $this->render('SeleyaBundle:Bookmark:index.html.twig', array(
            'bookmarks' => $bookmarks
        ));
    }
    
    /**
     * Deletes a bookmark
     *  
     * @param int $id ID of the bookmark
     * @return Rendered template
     * 
     * @Secure(roles="ROLE_USER")
     * @Route("/delete/{id}", name="bookmark_delete", options={"expose"=true})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $em->getRepository('SeleyaBundle:User')
                   ->getUser($securityContext->getToken()->getUser()->getUsername());

        $bookmark = $em->getRepository('SeleyaBundle:Bookmark')->findOneById($id);
        if ($bookmark === null || $user === null) {
            return new Response('', 400, array('Content-Type'=>'application/json'));
        }

        if ($bookmark->getUser()->getId() != $user->getId()) {
            return new Response(
                json_encode(
                    array(
                        'success'  => false
                    )
                ), 
                200, 
                array(
                    'Content-Type' => 'application/json'
                )
            );
        }
        
        $em->remove($bookmark);
        $em->flush();
        
        return new Response(
            json_encode(
                array(
                    'success'  => true
                )
            ), 
            200, 
            array(
                'Content-Type' => 'application/json'
            )
        );
    }
    
    /**
     * Toggles a bookmark: 
     * Set a bookmark if currently not set, 
     * delete bookmark if currently set
     * 
     * @param int $id ID of the record
     *
     * @Secure(roles="ROLE_USER")
     * @Route("/toggle/{id}", name="bookmark_toggle", options={"expose"=true})
     */
    public function toggleBookmarkAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SeleyaBundle:Record')
                     ->getRecord($id);
        
        $securityContext = $this->get('security.context');
        $user = $em->getRepository('SeleyaBundle:User')
                   ->getUser($securityContext->getToken()->getUser()->getUsername());
        
        if ($record === null) {
            return new Response('', 400, array('Content-Type'=>'application/json'));
        }

        $hasBookmark = false;
        $bookmark = $em->getRepository('SeleyaBundle:Bookmark')
                       ->getBookmarkForRecord($record, $user);
        if ($bookmark === null) {
            $bookmark = new Bookmark();
            $bookmark->setRecord($record);
            $bookmark->setUser($user);
            $em->persist($bookmark);
            $em->flush();
            $hasBookmark = true;
        } else {
            $em->remove($bookmark);
            $em->flush();
        }
        
        return new Response(
            json_encode(
                array(
                    'success'  => true,
                    'bookmark' => $hasBookmark
                )
            ), 
            200, 
            array(
                'Content-Type' => 'application/json'
            )
        );
    }
}
