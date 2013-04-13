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
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\OneToMany(targetEntity="Bookmark", mappedBy="record", cascade={"all"})
     */
    protected $bookmarks;
    
    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="records")
     */
    protected $course;
    
    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="Record_Lecturers") 
     */
    protected $lecturers;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="records")
     * @ORM\JoinTable(name="Record_Users") 
     */
    protected $users;
    
    /**
     * @ORM\OneToMany(targetEntity="View", mappedBy="record", cascade={"all"})
     */
    protected $views;
    
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
        $this->metadata = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->lecturers = new ArrayCollection();
        $this->views = new ArrayCollection();
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
    
    public function getBookmarks()
    {
        return $this->bookmarks;
    }
    
    public function getCourse()
    {
        return $this->course;
    }
    
    public function setCourse($course)
    {
        $this->course = $course;
    }
    
    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }
    
    public function getViews()
    {
        return $this->views;
    }

    public function getLecturers()
    {
        return $this->lecturers;
    }

    public function setLecturers($lecturers)
    {
        $this->lecturers = $lecturers;
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
