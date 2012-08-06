<?php
namespace Project\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
*/
class Img
{
	/**
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	/**
	* @ORM\Column(type="string", nullable="true", length="255")
	*/
	protected $title;
	
	/**
	* @ORM\Column(type="text", nullable="true")
	*/
	protected $info;
	/**
	* @ORM\Column(type="string", length="255")
	*/
	protected $pass;

	/**
     * @ORM\OneToOne(targetEntity="User")
     * 
     */
    private $user;
    
     /**
     * @ORM\ManyToMany(targetEntity="Gallery")
     * @ORM\JoinTable(name="img_gallery",
     *     joinColumns={@ORM\JoinColumn(name="img_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection $imgGallery
     */
    protected $imgGallery;
    
  
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
     * Set pass
     *
     * @param string $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set user
     *
     * @param Project\DataBundle\Entity\User $user
     */
    public function setUser(\Project\DataBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Project\DataBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add imgGallery
     *
     * @param Project\DataBundle\Entity\Gallery $imgGallery
     */
    public function addGallery(\Project\DataBundle\Entity\Gallery $imgGallery)
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
}