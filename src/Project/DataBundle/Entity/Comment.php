<?php
namespace Project\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
*/
class Comment
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
	protected $text;
	/**
	* @ORM\Column(type="datetime")
	*/
	protected $date;

	/**
	* @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
	* @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	*/
    private $user;
   
   
   /**
	* @ORM\ManyToOne(targetEntity="Gallery", inversedBy="comments")
	* @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
	*/
    private $gallery;
   
    /**
	* @ORM\ManyToOne(targetEntity="News", inversedBy="comments")
	* @ORM\JoinColumn(name="news_id", referencedColumnName="id")
	*/
    private $news;
    
    /**
	* @ORM\ManyToOne(targetEntity="Video", inversedBy="comments")
	* @ORM\JoinColumn(name="video_id", referencedColumnName="id")
	*/
    private $video;
    
    /**
	* @ORM\ManyToOne(targetEntity="Img", inversedBy="comments")
	* @ORM\JoinColumn(name="img_id", referencedColumnName="id")
	*/
    private $img;
  
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
     * Set text
     *
     * @param text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return text 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate()
    {
        // WILL be saved in the database
        $this->date = new \DateTime("now");
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
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
     * Set gallery
     *
     * @param Project\DataBundle\Entity\Gallery $gallery
     */
    public function setGallery(\Project\DataBundle\Entity\Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get gallery
     *
     * @return Project\DataBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set news
     *
     * @param Project\DataBundle\Entity\News $news
     */
    public function setNews(\Project\DataBundle\Entity\News $news)
    {
        $this->news = $news;
    }

    /**
     * Get news
     *
     * @return Project\DataBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set video
     *
     * @param Project\DataBundle\Entity\Video $video
     */
    public function setVideo(\Project\DataBundle\Entity\Video $video)
    {
        $this->video = $video;
    }

    /**
     * Get video
     *
     * @return Project\DataBundle\Entity\Video 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set img
     *
     * @param Project\DataBundle\Entity\Img $img
     */
    public function setImg(\Project\DataBundle\Entity\Img $img)
    {
        $this->img = $img;
    }

    /**
     * Get img
     *
     * @return Project\DataBundle\Entity\Img 
     */
    public function getImg()
    {
        return $this->img;
    }
}