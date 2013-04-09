<?php

namespace kvibes\SeleyaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="kvibes\SeleyaBundle\Repository\RecordRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $externalId;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $title;
    
    /**
     * @ORM\Column(type="date")
     */
    protected $recordDate;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $visible;

    /**
     * @ORM\OneToMany(targetEntity="Metadata", mappedBy="record", cascade={"all"})
     */
    protected $metadata;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="record", cascade={"all"})
     * @ORM\OrderBy({"created" = "ASC"})
     */
    protected $comments;
    
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
        $this->metadata = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->visible = false;
        $this->recordDate = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getExternalId()
    {
        return $this->externalId;
    }
    
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    public function setMetadata(ArrayCollection $metadata)
    {
        $this->metadata = $metadata;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getRecordDate()
    {
        return $this->recordDate;
    }
    
    public function setRecordDate($recordDate)
    {
        $this->recordDate = $recordDate;
    }
    
    public function isVisible()
    {
        return $this->visible;
    }
    
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }
    
    public function getComments()
    {
        return $this->comments;
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
    
    public function downloadPreviewImage($url)
    {
        $saveFileName = $this->getUploadDir() . '/' . $this->getExternalId() . '.jpg';
        $ch = curl_init($url);
        $fp = fopen($saveFileName, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp); 
    }

    protected function getUploadDir()
    {
        return __DIR__.'/../../../../web/previews';
    }
}
