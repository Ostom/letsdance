<?php

namespace Project\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\DataBundle\Entity\User;
use Project\DataBundle\Entity\Dancetype;
use Symfony\Component\HttpFoundation\Request;

require_once getcwd().'/letsdance/static/php/upload.php';
use Project\DataBundle\Module\FormCheck;


class DefaultController extends Controller
{
    /**
     * @Route("/registration")
     * @Template()
     */
    public function registrationAction()
    {
		$name= '';
		$em = $this->getDoctrine()->getEntityManager();			
		$dancetypes = $em->getRepository('ProjectDataBundle:Dancetype')->findAll();
				
		if(isset($_REQUEST['_username']))
		{
			
			$request = Request::createFromGlobals();		
			$request->getPathInfo();		
			
			$id_dancetype = $_POST['_typedance'];
			$name = $request->request->get('_username');
			$pass = $request->request->get('_password');
			
			// is Empty
			if (($pass == '')||($name == '') ) return array('name'=>'Fill the gaps');		
			
			$a = new FormCheck();
			$user = new User();
			//file
			if($_FILES['filename']['name'] != '')
				{$ka = $a->uploadFile(); $user->setImg($ka);}
			else{ $user->setImg(getcwd().'/letsdance/static/files/'.'default.png'); }
			 
			$user->setUsername($name);
			//password
			$factory = $this->get('security.encoder_factory');
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($pass, $user->getSalt());
			$user->setPassword($password);
			
			$user->setRoles(array('ROLE_USER'));
			
			//Set Dance type
			$dancetype = $em->getRepository('ProjectDataBundle:Dancetype')->find($id_dancetype);
			$user->setDancetype($dancetype);
					
			// Sync with database
			$em->persist($user);
			$em->flush();
		}
        return array('name' => $name, 'dancetypes' => $dancetypes);
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
				//$user1 = $em->getRepository('ProjectDataBundle:User')->findOneByUsername('den');
				//$user2 = $em->getRepository('ProjectDataBundle:User')->findOneByUsername('user');
				//$dance->addUser($user1);
				//$dance->addUser($user2);		
				//$dance = $em->getRepository('ProjectDataBundle:Dancetype')->find(1);
				//$us = $dance->getUsers();
				$responce = $dance -> getTitle();
				$em->persist($dance);
				$em->flush();
			}
						
		}
		return array('dance' => $responce);
	}
	 /**
     * @Route("/isdance")
     * @Template()
     */
    public function isdanceAction(){
		$em = $this->getDoctrine()->getEntityManager();
		$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername('hhh');
		$a = $user->getDancetype();
		echo($a->getTitle());
		return array('dance' => $a);
	}
	/**
     * @Route("/isuser")
     * @Template()
     */
    public function isuserAction(){
		$em = $this->getDoctrine()->getEntityManager();
		$dance = $em->getRepository('ProjectDataBundle:Dancetype')->find(1);
		$a = $dance->getUser();
		//echo($a[0]);
		return array('dance' => $a);
	}
}
