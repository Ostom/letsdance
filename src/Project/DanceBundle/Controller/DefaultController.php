<?php

namespace Project\DanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;


class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Route("/hello/")
     * @Template()
     */
    public function indexAction($name = Null){
        //Something like a main page
        if ($name)
        return array('name' => $name);
        else 
        return array('name' => 'Dude');
    }
    /**
     * @Route("/room/")
     * @Template()
     */
    public function roomAction($name = Null){       
		//room of User
		//TODO: Make the div-block ( absolute position ) for edit all types of information 
		$c = $this->container->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
		// It's only for testing this application on the windows machins
		$patch = $user->getImg()->getPass();
		$patch = str_replace( '\\', '/', $patch);
		$patch = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://'.$_SERVER['HTTP_HOST'], $patch);
        //Generate News
        
        // Get The list of News by all Users from Type of dance and ordering by Date 
		$query = $em->createQuery(
		"SELECT n FROM ProjectDataBundle:News n JOIN n.user u JOIN u.dancetype d WHERE d.title = :dancetype ORDER BY n.date DESC"
		)-> setParameter('dancetype', $user->getDancetype()->getTitle()); 
		$news = $query->getResult();
		
        return array('user' => $user, 'img' => $patch, 'news_list' => $news );
    }
    
    /**
     * @Route("/videolist/{name}")
     * @Route("/videolist/")
     * @Template()
     */
    public function videolistAction($id = Null){       
		// The panel for manage the user's video 
		$em = $this->getDoctrine()->getEntityManager();
		$videos = $em->getRepository('ProjectDataBundle:Video')->findAll();
		if ($id){
			$c = $this->container->get('security.context')->getToken()->getUser();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$video = $em->getRepository('ProjectDataBundle:Video')->find($id);
			$user->addVideo($video);
			$em->persist($user);
			$em->flush();
		}
		return array('video' => $videos);
    }
    /**
     * @Route("/rmvideolist/{name}")
     * @Route("/rmvideolist/")
     * @Template()
     */
    public function rmvideolistAction($name = Null){       
		$em = $this->getDoctrine()->getEntityManager();
		$c = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
		$video = $user->getUserVideo();
		echo($video[0]->getTitle());
		if ($name){
			unset($video[$name]);
			$em->persist($user);
			$em->flush();
		}
		return array('video' => $video);
    }
    
    /**
     * @Route("/login")
     * @Template()
     */
	public function loginAction(){
		$request = $this->getRequest();
		$session = $request->getSession();
			if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)){
				$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
			}
			else{
				$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			}
		return array(
		'last_username' => $session->get(SecurityContext::LAST_USERNAME),
		'error'
		=> $error,
		);
	}
}
