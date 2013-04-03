<?php

namespace kvibes\SeleyaBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function indexAction()
    {
        return $this->render('SeleyaBundle:Admin:index.html.twig');
    }
}
