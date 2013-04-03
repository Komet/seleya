<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use kvibes\SeleyaBundle\Entity\MetadataConfigDefinition;
use kvibes\SeleyaBundle\Entity\MetadataConfigOption;

/**
 * @ORM\Entity(repositoryClass="kvibes\SeleyaBundle\Repository\MetadataConfigRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataConfig
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="MetadataConfigDefinition")
     */
    protected $definition;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $displayOrder;
    
    /**
     * @ORM\OneToMany(targetEntity="MetadataConfigOption", mappedBy="metadataConfig", cascade={"persist", "remove"})
     */
    protected $options;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;    
    
    public function __construct()
    {
        $this->options = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getDefinition()
    {
        return $this->definition;
    }
    
    public function setDefinition(MetadataConfigDefinition $definition = null)
    {
        $this->definition = $definition;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOptions($options)
    {
        foreach ($options as $option) {
            $option->setMetadataConfig($this);
        }
        
        $this->options = $options;
    }
    
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
    
    public function setDisplayOrder($order)
    {
        $this->displayOrder = $order;
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated = new \DateTime();
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }            
}