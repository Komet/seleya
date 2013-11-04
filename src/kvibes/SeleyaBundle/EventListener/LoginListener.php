<?php

namespace kvibes\SeleyaBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user && get_class($user) == 'IMAG\LdapBundle\User\LdapUser') {
            $this->doctrine
                 ->getManager()
                 ->getRepository('SeleyaBundle:User')
                 ->insertOrRefreshUser(
                     $user->getUsername(),
                     $user->getAttribute('cn'),
                     in_array('ROLE_ADMIN', $user->getRoles())
                 );
        }
    }
}
