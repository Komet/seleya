<?php

namespace kvibes\SeleyaBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use IMAG\LdapBundle\Event\LdapUserEvent;

class LdapSecurityListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            \IMAG\LdapBundle\Event\LdapEvents::PRE_BIND => 'onPreBind',
        );
    }

    /**
     * @todo: adjust employeetype to mluPersonType
     */
    public function onPreBind(LdapUserEvent $event)
    {
        $user = $event->getUser();
        $attrs = $user->getAttributes();
        if (array_key_exists('employeetype', $attrs)) {
            if ($attrs['employeetype'] == 1) {
                $user->addRole('ROLE_ADMIN');
            } elseif ($attrs['employeetype'] == 2 || $attrs['employeetype'] == 3) {
                $user->addRole('ROLE_USER');
            }
        }
    }
}
