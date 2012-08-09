<?php

namespace Project\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\DataBundle\Entity\User;
use Project\DataBundle\Entity\Video;
use Project\DataBundle\Entity\Img;
use Project\DataBundle\Entity\Gallery;
use Project\DataBundle\Entity\News;
use Project\DataBundle\Entity\Comment;

use Project\DataBundle\Entity\Dancetype;
use Symfony\Component\HttpFoundation\Request;

require_once getcwd().'/letsdance/static/php/upload.php';
require_once getcwd().'/letsdance/static/php/youtube.php';
use Project\DataBundle\Module\FormCheck;
use Project\DataBundle\Module\Tube;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/registration/{change}")
     * @Route("/registration")
     * @Template()
     */
    public function registrationAction($change = 'nochange'){
		//It's a very big and confused action of registration users
		$name='';
		$em = $this->getDoctrine()->getEntityManager();			
		$dancetypes = $em->getRepository('ProjectDataBundle:Dancetype')->findAll();
				
			if(isset($_REQUEST['_username']))
			{
				
				$request = Request::createFromGlobals();		
				$request->getPathInfo();		
				if(isset($_POST['_typedance']))
					$id_dancetype = $_POST['_typedance'];
				$name = $request->request->get('_username');
				$pass = $request->request->get('_password');
				
				if($change == 'change'){
					// Get user from session
					$c = $this->container->get('security.context')->getToken()->getUser();
					$em = $this->getDoctrine()->getEntityManager();
					$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
				}
				else {
					$em = $this->getDoctrine()->getEntityManager();
					// Get last user's id
					$query = $em->createQuery(
					'SELECT Max(u.id) FROM ProjectDataBundle:User u'
					); 
					$pr = $query->getResult();
					$last_user_id = $pr[0][1]; 		
					$user = new User();
				}
				
				// User Pass - is not Empty
				if ($change != 'change'){
					if (($pass == '')||($name == '') ) return array('name'=>'Fill the gaps', 'dancetypes'=> dancetypes);		
					$user->setUsername($name);
					//password
					$factory = $this->get('security.encoder_factory');
					$encoder = $factory->getEncoder($user);
					$password = $encoder->encodePassword($pass, $user->getSalt());
					$user->setPassword($password);
				}
				
				
				// Image for User to Gallery 

				$Check = new FormCheck();
				$Img = new Img;
				$Gallery = $em->getRepository('ProjectDataBundle:Gallery')->findOneByTitle('user');
				if($change == 'change'){
					if($_FILES['filename']['name'] != ''){
						$im_pass = $Check->uploadFile('user',$user->getId()); 							
						$Img->setPass($im_pass);
						$em->persist($Img);
						$em->flush();
					
						if($Gallery){ 
							$Gallery->addImg($Img);
						} 
						else {
							$Gallery = new Gallery; 
							$Gallery->setTitle('user');
							$Gallery->addImg($Img);
							$em->persist($Gallery);
							echo ('not ok<br/>');
						}
						$user->setImg($Img);
						
						$em->persist($Img);
						$em->flush();
					}
				}
				else{
					if($_FILES['filename']['name'] != ''){
						$im_pass = $Check->uploadFile('user',$last_user_id + 1);						
						$Img->setPass($im_pass);
							$em->persist($Img);
							$em->flush();
						
						if($Gallery){ 
							$Gallery->addImg($Img);
						} 
						else {
							$Gallery = new Gallery; 
							$Gallery->setTitle('user');
							$Gallery->addImg($Img);
							$em->persist($Gallery);
							$em->flush();
							echo ('not ok<br/>');
						}
						$user->setImg($Img);
					}
					else{
						$im_pass = getcwd().'/letsdance/static/files/'.'default.png';							
						$Img->setPass($im_pass);
						$em->persist($Img);
						$em->flush();

						if($Gallery){ 
							$Gallery->addImg($Img);
						} 
						else {
							$Gallery = new Gallery; 
							$Gallery->setTitle('user');
							$Gallery->addImg($Img);
							$em->persist($Gallery);
							$em->flush();
							echo ('not ok<br/>');
						}
						$user->setImg($Img);
					}
				}
				// end of set image... Oo i think it can be solved easly....				
				$user->setRoles(array('ROLE_USER'));
				
				//Set Dance type
				if(isset($_POST['_typedance'])){
					$dancetype = $em->getRepository('ProjectDataBundle:Dancetype')->find($id_dancetype);
					$user->setDancetype($dancetype);
				}
				//Set unnessesary info
				if(isset($_REQUEST['icq']))$user->setIcq($_REQUEST['icq']);
				if(isset($_REQUEST['skype']))$user->setSkype($_REQUEST['skype']);
				if(isset($_REQUEST['info']))$user->setInfo($_REQUEST['info']);
				
				// Sync with database
				$em->persist($user);
				$em->flush();
			}       
        return array('name' => $name, 'dancetypes' => $dancetypes, 'change' => $change);
    }
    
    /**
     * @Route("/albums")
     * @Template()
     */
    public function albumsAction(){
		// This action give you a possibility to choose your album from your
		// album list and connected with the next action
		$em = $this->getDoctrine()->getEntityManager();
		$c = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
		
		$Albums = $user -> getGalleryUser();		
		if (isset($_REQUEST['create'])){
			if($_REQUEST['create'] == true){
				//Add the new albom
				$Gallery = new Gallery;
				$Gallery->setTitle($_REQUEST['title']);
				if (isset($_REQUEST['info'])) $Gallery->setInfo($_REQUEST['info']);
				$Gallery->addUser($user);
				$em->persist($Gallery);
				$em->flush();
			}
		}
		return array('albums' => $Albums);
	}
	
	 /**
     * @Route("/album/{id}")
     * @Template()
     */
    public function albumAction($id){
		// TODO: very difficult way to do more easly
		// TODO: Possibility of giving the acces for other users, that owner's like
		$em = $this->getDoctrine()->getEntityManager();	
		$Gallery = $em->getRepository('ProjectDataBundle:Gallery')->find($id);
		$Imgs = $Gallery->getImgGallery();
		//echo ($Imgs[2]->getId());
		//Add the photo
		$Check = new FormCheck();
		if (isset($_FILES['filename']))
		if($_FILES['filename']['name'] != ''){
			// last img id
			$query = $em->createQuery('SELECT Max(u.id) FROM ProjectDataBundle:Img u'); 
			$pr = $query->getResult();
			$last_img_id = $pr[0][1];
			// new object 
			$Img = new Img;		
			$im_pass = $Check->uploadFile($Gallery->getTitle(),$last_img_id + 1); 							
			$Img->setTitle($_REQUEST['title']);
			if (isset($_REQUEST['info'])) $Img->setInfo($_REQUEST['info']);
			// the right view
			$patch = str_replace( '\\', '/', $im_pass);
			$patch = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://'.$_SERVER['HTTP_HOST'], $patch);
			// set img
			$Img->setPass($patch);			
			$Img->addGallery($Gallery);
			$em->persist($Img);
			$em->flush();
		}
		return array('imgs' => $Imgs, 'id'=>$id);
	}

    /**
     * @Route("/obrabotka")
     */
     public function obrabotkaAction(){
		//The test action, can be deleted
		if(isset($_REQUEST['name'])) echo ($_REQUEST['name']);
		echo('no');
		return new Response(); 
	 }
	 
    /**
     * @Route("/admin/newdance")
     * @Template()
     */
    public function dancetypeAction(){
		// Create a new type of dance
		$responce = '';
		if(isset($_REQUEST['title'])){
			$title = $_REQUEST['title'];
			$info = $_REQUEST['info'];
			if (( $title != '') && ( $info != '')){
				$dance = new Dancetype;
				$dance->setTitle($title);
				$dance->setInfo($info);
				$em = $this->getDoctrine()->getEntityManager();
				$responce = $dance -> getTitle();
				$em->persist($dance);
				$em->flush();
			}
		}
		return array('dance' => $responce);
	}
	
    /**
     * @Route("/createnews")
     * @Template()
     */
    public function createnewsAction(){
		// TODO: It's must be changed for using through AJAX methods 
		$responce = '';
		//Get user and news list
			$em = $this->getDoctrine()->getEntityManager();
			$c = $this->container->get('security.context')->getToken()->getUser();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$NewsList = $user -> getNews();
		if(isset($_REQUEST['title']))
		{
			$title = $_REQUEST['title'];
			$text = $_REQUEST['text'];
			if (( $title != '') && ( $text != ''))
			{
				// Create News
				$news = new News;
				$news->setTitle($title);
				$news->setText($text);
				$news->setDate();
				$news->setUser($user);
				$em->persist($news);
				$em->flush();
			}
						
		}
		return array('news' => $NewsList);
	}
	
	/**
     * @Route("/comment_news/{id}")
     * @Template()
     */
    public function comment_newsAction($id){
		$responce = '';
		//Get user and news list
			$em = $this->getDoctrine()->getEntityManager();
			$c = $this->container->get('security.context')->getToken()->getUser();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$news = $em->getRepository('ProjectDataBundle:News')->find($id);
		if(isset($_REQUEST['title']))
		{
			$title = $_REQUEST['title'];
			$text = $_REQUEST['text'];
			if (( $title != '') && ( $text != ''))
			{
				// Create News
				$com = new Comment;
				$com->setTitle($title);
				$com->setText($text);
				$com->setDate();
				$com->setUser($user);
				$com->setNews($news);
				$em->persist($com);
				$em->flush();
			}		
		}
		return array('comment' => $com);
	}
	
	 /**
     * @Route("/youtube")
     * @Template()
     */
	public function youtubeAction(){
		// Add a new video
		$path = '';
		if(isset($_REQUEST['path']))
		{			
			$em = $this->getDoctrine()->getEntityManager();	
			// generate a path
			$tube = new Tube;
			$path = $tube->Get($_REQUEST['path']);
			$path = $tube->Generate($path);
			// create video entity
			$video = new Video;
			$video->setTitle('new');
			$video->setPass($path);
			$em->persist($video);
			$em->flush();
			// Add Video to user's video list
			$c = $this->container->get('security.context')->getToken()->getUser();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$user->addVideo($video);
			$em->persist($user);
			$em->flush();
		}
		return array( 'video' => $path );
	}
	/**
     * @Route("/help")
     * @Template()
     */
     public function helpAction(){
		 // Action for testing any code
		 
		 return new Response();
	 }
}
