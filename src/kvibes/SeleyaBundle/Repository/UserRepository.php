<?php

namespace kvibes\SeleyaBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use kvibes\SeleyaBundle\Entity\User;

class UserRepository extends EntityRepository
{
    public function getUser($username)
    {
        return $this->findOneByUsername($username);
    }
    
    public function insertOrRefreshUser($username, $commonName)
    {
        $user = $this->findOneByUsername($username);
        if ($user === null) {
            $this->insertUser($username, $commonName);
        } else {
            $this->updateUser($user, $commonName);
        }
    }
    
    private function insertUser($username, $commonName)
    {
        $user = new User($username);
        $user->setCommonName($commonName);
        $user->setLastLogin(new \DateTime());

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
    
    private function updateUser($user, $commonName)
    {
        $user->setCommonName($commonName);
        $user->setLastLogin(new \DateTime());
        $em = $this->getEntityManager();
        $em->flush();
    }
}
