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
        if ($user) {
            /*
            $user->setLastLogin(new \DateTime());
            $user->setNumberOfLogins($user->getNumberOfLogins() + 1);
            $em = $this->doctrine->getEntityManager();
            $em->flush();
             * 
             */
        }
    }
}
