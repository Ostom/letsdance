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
		//$name= 'hui';
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
		//Template at the start list of your albums(need to create users oneToMany albom or give the acces for users) and you 
		// can choose the albom and redakt it 
		// or switch a button "create" and create a new albom
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
			else if ($_REQUEST['create'] == false){
				//Add the photo
				$Check = new FormCheck();
				$Img = new Img;
				$Gallery = $em->getRepository('ProjectDataBundle:Gallery')->find($_REQUEST['id']);
				if($_FILES['filename']['name'] != ''){
					$im_pass = $Check->uploadFile($Gallery->getTitle(),$Gallery->getId()); 							
					$Img->setPass($im_pass);
					$em->persist($Img);
					$em->flush();
				}
				$Gallery->addImg($img);
			}
			echo($_REQUEST['create'] );
		}
		//echo($_REQUEST['create'] );
		return array('albums' => $Albums);
	}
	
	 /**
     * @Route("/album/{id}")
     * @Template()
     */
    public function albumAction($id){
		//Template at the start list of your albums(need to create users oneToMany albom or give the acces for users) and you 
		// can choose the albom and redakt it 
		// or switch a button "create" and create a new albom
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
     * @Route("/admin/newdance")
     * @Template()
     */
    public function dancetypeAction(){
		$responce = '';
		if(isset($_REQUEST['title']))
		{
			$title = $_REQUEST['title'];
			$info = $_REQUEST['info'];
			if (( $title != '') && ( $info != ''))
			{
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
				//$responce = $dance -> getTitle();
				$em->persist($news);
				$em->flush();
			}
						
		}
		return array('news' => $NewsList);
	}
	
	/**
     * @Route("/isdance")
     */
    public function isdanceAction(){
		$em = $this->getDoctrine()->getEntityManager();
		$dance = $em->getRepository('ProjectDataBundle:Dancetype')->find(1);
		$a = $dance->getUser();
		//$x=true;
		//$a->isDirty($x);
		echo(//$dance->getTitle());
		$a->get(0)->getUsername());
		return new Response();//$this->render('ProjectDataBundle:Default:youtube.html.twig');
	}
	 /**
     * @Route("/youtube")
     * @Template()
     */
	public function youtubeAction(){
		$responce = '';
		
		if(isset($_REQUEST['path']))
		{			
			$em = $this->getDoctrine()->getEntityManager();	
			
			$tube = new Tube;
			$path = $tube->Get($_REQUEST['path']);
			$responce = $tube->Generate($path);
			
			$video = new Video;
			$video->setTitle('new');
			$video->setPass($responce);
			$em->persist($video);
			$em->flush();
			
			$c = $this->container->get('security.context')->getToken()->getUser();
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$user->addVideo($video);
			$em->persist($user);
			$em->flush();
		}
		return array( 'video' => $responce );
	}
	/**
     * @Route("/help")
     * @Template()
     */
     public function helpAction(){
		 $em = $this->getDoctrine()->getEntityManager();
					$query = $em->createQuery(
					'SELECT Max(u.id) FROM ProjectDataBundle:User u'
					); 
					$pr = $query->getResult();
					$last_user_id = $pr[0][1]; 
					
		 return new Response();
	 }
}
