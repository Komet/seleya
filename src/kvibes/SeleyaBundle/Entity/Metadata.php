<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use kvibes\SeleyaBundle\Entity\Record;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Metadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MetadataConfig")
     */
    protected $config;
    
    /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="metadata")
     * @Assert\Type(type="Record")
     */
    protected $record;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataString", cascade={"all"})
     * @Assert\Type(type="MetadataString")
     */
    private $string;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataText", cascade={"all"})
     * @Assert\Type(type="MetadataText")
     */
    private $text;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataDate", cascade={"all"})
     * @Assert\Type(type="MetadataDate")
     */
    private $date;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataTime", cascade={"all"})
     * @Assert\Type(type="MetadataTime")
     */
    private $time;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataDateTime", cascade={"all"})
     * @Assert\Type(type="MetadataDateTime")
     */
    private $datetime;
    
    /**
     * @ORM\OneToOne(targetEntity="MetadataNumber", cascade={"all"})
     * @Assert\Type(type="MetadataNumber")
     */
    private $number;

    /**
     * @ORM\OneToOne(targetEntity="MetadataBoolean", cascade={"all"})
     * @Assert\Type(type="MetadataBoolean")
     */
    private $boolean;
    
    /**
     * @ORM\ManyToOne(targetEntity="MetadataConfigOption")
     */
    private $option;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    public function getRecord()
    {
        return $this->record;
    }
    
    public function setRecord(Record $record)
    {
        $this->record = $record;
    }
    
    public function getValue()
    {
        switch ($this->getConfig()->getDefinition()->getId()) {
            case 'text':
            case 'url':
                return $this->getString()->getValue();
                break;
            case 'textarea':
                return $this->getText()->getValue();
                break;
            case 'date':
                return $this->getDate()->getValue();
                break;
            case 'time':
                return $this->getTime()->getValue();
                break;
            case 'datetime':
                return $this->getDateTime()->getValue();
                break;
            case 'number':
                return $this->getNumber()->getValue();
                break;
            case 'checkbox':
                return $this->getBoolean()->getValue();
                break;
            case 'select':
                return $this->getOption();
                break;
        }
    }
    
    public function setValue($value)
    {
        switch ($this->getConfig()->getDefinition()->getId()) {
            case 'text':
            case 'url':
                $this->getString()->setValue($value);
                break;
            case 'textarea':
                $this->getText()->setValue($value);
                break;
            case 'date':
                $this->getDate()->setValue($value);
                break;
            case 'time':
                $this->getTime()->setValue($value);
                break;
            case 'datetime':
                $this->getDateTime()->setValue($value);
                break;
            case 'number':
                $this->getNumber()->setValue($value);
                break;
            case 'checkbox':
                $this->getBoolean()->setValue($value);
                break;
            case 'select':
                $this->option = $value;
                break;
        }
    }
    
    private function getString()
    {
        if ($this->string === null) {
            $this->string = new MetadataString();
        }
        return $this->string;
    }

    private function getText()
    {
        if ($this->text === null) {
            $this->text = new MetadataText();
        }
        return $this->text;
    }

    private function getDate()
    {
        if ($this->date === null) {
            $this->date = new MetadataDate();
        }
        return $this->date;
    }

    private function getTime()
    {
        if ($this->time === null) {
            $this->time = new MetadataTime();
        }
        return $this->time;
    }

    private function getDateTime()
    {
        if ($this->datetime === null) {
            $this->datetime = new MetadataDateTime();
        }
        return $this->datetime;
    }

    private function getNumber()
    {
        if ($this->number === null) {
            $this->number = new MetadataNumber();
        }
        return $this->number;
    }
    
    private function getBoolean()
    {
        if ($this->boolean === null) {
            $this->boolean = new MetadataBoolean();
        }
        return $this->boolean;
    }
    
    private function getOption()
    {
        return $this->option;
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
