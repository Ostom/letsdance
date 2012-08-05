<?php

namespace Project\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\DataBundle\Entity\User;
use Project\DataBundle\Entity\Video;
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
		$name= 'hui';
		$em = $this->getDoctrine()->getEntityManager();			
		$dancetypes = $em->getRepository('ProjectDataBundle:Dancetype')->findAll();
				
			if(isset($_REQUEST['_username']))
			{
				//$name='1';
				$request = Request::createFromGlobals();		
				$request->getPathInfo();		
				if(isset($_POST['_typedance']))
					$id_dancetype = $_POST['_typedance'];
				$name = $request->request->get('_username');
				$pass = $request->request->get('_password');
				
				if($change == 'change'){
					$c = $this->container->get('security.context')->getToken()->getUser();
					$em = $this->getDoctrine()->getEntityManager();
					$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
				}
				else {
					$em = $this->getDoctrine()->getEntityManager();
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
				
				//file
				
				$a = new FormCheck();
				if($change == 'change'){
					if($_FILES['filename']['name'] != '')
						{$ka = $a->uploadFile('user',$user->getId()); $user->setImg($ka);}
				}
				else{
					if($_FILES['filename']['name'] != '')
						{$ka = $a->uploadFile('user',$last_user_id + 1); $user->setImg($ka);}
					else{ $user->setImg(getcwd().'/letsdance/static/files/'.'default.png'); }
				}
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
