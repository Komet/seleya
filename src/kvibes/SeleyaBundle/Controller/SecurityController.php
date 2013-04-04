<?php

namespace kvibes\SeleyaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'SeleyaBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
    
    public function frontendLoginAction()
    {
        $securityContext = $this->get('security.context');
        if ($securityContext->isGranted('ROLE_USER')) {
            $user = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('SeleyaBundle:User')
                         ->getUser($securityContext->getToken()->getUser()->getUsername());
            return $this->render(
                'SeleyaBundle:Security:frontend_loggedIn.html.twig',
                array(
                    'user' => $user
                )
            );
        } else {
            $request = $this->getRequest();
            $session = $request->getSession();
    
            // get the login error if there is one
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
                );
            } else {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            }
    
            return $this->render(
                'SeleyaBundle:Security:frontend_login.html.twig',
                array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error'         => $error,
                )
            );
        }
    }
}
