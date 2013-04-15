<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToMany(targetEntity="Record", mappedBy="users")
     */
    private $records;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogin;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;
    
    public function __construct($username)
    {
        $this->username = $username;
        $this->lastLogin = new \DateTime();
        $this->records = new ArrayCollection();
        $this->admin = false;
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
    
    public function getRecords()
    {
        return $this->records;
    }
    
    public function isAdmin()
    {
        return $this->admin;
    }
    
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }
    
    public function __toString()
    {
        return $this->getCommonName() . ' (' . $this->getUsername() . ')';
    }
}
