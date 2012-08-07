<?php
namespace Project\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
*/
class Gallery
{
	/**
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	/**
	* @ORM\Column(type="string", length="255")
	*/
	protected $title;
	
	/**
	* @ORM\Column(type="text", nullable="true")
	*/
	protected $info;
	
	/**
     * @ORM\ManyToMany(targetEntity="Img", mappedBy="imgGallery")
     */
    private $imgGallery;
    
     /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="gallery_user",
     *     joinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection $galleryUser
     */
    protected $galleryUser;
    
    /**
	* @ORM\OneToMany(targetEntity="Comment", mappedBy="gallery")
	*/
	protected $comments;
    
    public function __construct()
    {
        $this->imgGallery = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set info
     *
     * @param text $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * Get info
     *
     * @return text 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Add imgGallery
     *
     * @param Project\DataBundle\Entity\Img $imgGallery
     */
    public function addImg(\Project\DataBundle\Entity\Img $imgGallery)
    {
        $this->imgGallery[] = $imgGallery;
    }

    /**
     * Get imgGallery
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getImgGallery()
    {
        return $this->imgGallery;
    }

    /**
     * Add galleryUser
     *
     * @param Project\DataBundle\Entity\User $galleryUser
     */
    public function addUser(\Project\DataBundle\Entity\User $galleryUser)
    {
        $this->galleryUser[] = $galleryUser;
    }

    /**
     * Get galleryUser
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGalleryUser()
    {
        return $this->galleryUser;
    }

    /**
     * Add comments
     *
     * @param Project\DataBundle\Entity\Comment $comments
     */
    public function addComment(\Project\DataBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}