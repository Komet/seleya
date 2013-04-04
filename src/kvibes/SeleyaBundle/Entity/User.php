<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="kvibes\SeleyaBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commonName;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogin;
    
    public function __construct($username)
    {
        $this->username = $username;
        $this->lastLogin = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getCommonName()
    {
        return $this->commonName;
    }
    
    public function setCommonName($commonName)
    {
        $this->commonName = $commonName;
    }
    
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
    
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }
}
