<?php
namespace Project\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
*/
class Video
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
	* @ORM\Column(type="string", length="255")
	*/
	protected $pass;
	/**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userVideo")
     */
    private $users;
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add users
     *
     * @param Project\DataBundle\Entity\User $users
     */
    public function addUser(\Project\DataBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}