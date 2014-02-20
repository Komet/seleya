<?php

namespace kvibes\SeleyaBundle\Controller;

use kvibes\SeleyaBundle\Entity\Bookmark;
use kvibes\SeleyaBundle\Entity\View;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * Searchs
     *
     * @return Rendered template
     *
     * @Route("/", name="search")
     */
    public function indexAction(Request $request)
    {
        $searchQuery = $request->get('searchQuery');
        $records = null;
        $error = '';
        if (strlen($searchQuery) < 3) {
            $error = 'tooShort';
        } else {
            $em = $this->getDoctrine()->getManager();
            $records = $em->getRepository('SeleyaBundle:Record')
                          ->search($searchQuery);
        }

        return $this->render(
            'SeleyaBundle:Search:results.html.twig',
            array(
                'records' => $records,
                'error'   => $error
            )
        );
    }
}
