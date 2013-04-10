<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin")
     */
    public function indexAction()
    {
        return $this->render('SeleyaBundle:Admin:index.html.twig');
    }
}
