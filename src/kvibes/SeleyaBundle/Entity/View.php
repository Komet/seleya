<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={@ORM\Index(name="search_idx", columns={"record_id", "date"})})
 */
class View
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="views")
     */
    private $record;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $viewCount;
    
    /**
     * @ORM\Column(type="date")
     */
    private $date;
    
    public function __construct()
    {
        $this->viewCount = 0;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getRecord()
    {
        return $this->record;
    }
    
    public function setRecord($record)
    {
        $this->record = $record;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getViewCount()
    {
        return $this->viewCount;
    }
    
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }
}
