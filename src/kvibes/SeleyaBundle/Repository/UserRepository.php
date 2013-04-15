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
    
    public function insertOrRefreshUser($username, $commonName, $admin)
    {
        $user = $this->findOneByUsername($username);
        if ($user === null) {
            $this->insertUser($username, $commonName, $admin);
        } else {
            $this->updateUser($user, $commonName, $admin);
        }
    }
    
    private function insertUser($username, $commonName, $admin)
    {
        $user = new User($username);
        $user->setCommonName($commonName);
        $user->setLastLogin(new \DateTime());
        $user->setAdmin($admin);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
    
    private function updateUser($user, $commonName, $admin)
    {
        $user->setCommonName($commonName);
        $user->setLastLogin(new \DateTime());
        $user->setAdmin($admin);
        $em = $this->getEntityManager();
        $em->flush();
    }
}
