<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="kvibes\SeleyaBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(type="string")
     */
    private $commonName;
    
    private $salt;
    private $password;
    
    public function __construct($username)
    {
        $this->username = $username;
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
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getSalt()
    {
        return $this->salt;
    }
    
    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }
    
    public function eraseCredentials()
    {
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->id
        ));
    }
    
    public function unserialize($serialized)
    {
        list(
            $this->id
        ) = unserialize($serialized);
    }
}
