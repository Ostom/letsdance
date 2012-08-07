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
    public function indexAction($name = Null)
    {
        if ($name)
        return array('name' => $name);
        else 
        return array('name' => 'Dude');
    }
    /**
     * @Route("/room/")
     * @Template()
     */
    public function roomAction($name = Null)
    {       
		$c = $this->container->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
		$patch = $user->getImg()->getPass();
		//$patch = 'hui';
		$patch = str_replace( '\\', '/', $patch);
		$patch = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://'.$_SERVER['HTTP_HOST'], $patch);
        //Generate News
        // Get The list of News by all Users from Type of dance? ordering by Date 
		$query = $em->createQuery(
		"SELECT u.username, n.title, n.text, n.date FROM ProjectDataBundle:News n JOIN n.user u JOIN u.dancetype d WHERE d.title = 'Balley' ORDER BY n.date DESC"
		//'SELECT Max(u.id) FROM ProjectDataBundle:User u'
		); 
		$news = $query->getResult();
		//$last_user_id = $pr[0][1]; 
        return array('user' => $user, 'img' => $patch, 'news_list' => $news );//get(SecurityContext::LAST_USERNAME));
    }
    /**
     * @Route("/videolist/{name}")
     * @Route("/videolist/")
     * @Template()
     */
    public function videolistAction($name = Null)
    {       
		$em = $this->getDoctrine()->getEntityManager();
		$video = $em->getRepository('ProjectDataBundle:Video')->findAll();
		
		if ($name){
			$c = $this->container->get('security.context')->getToken()->getUser();
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('ProjectDataBundle:User')->findOneByUsername($c->getUsername());
			$video1 = $em->getRepository('ProjectDataBundle:Video')->find($name);
			$user->addVideo($video1);
			$em->persist($user);
			$em->flush();
		}
		return array('video' => $video);
    }
    /**
     * @Route("/rmvideolist/{name}")
     * @Route("/rmvideolist/")
     * @Template()
     */
    public function rmvideolistAction($name = Null)
    {       
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
	public function loginAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
			if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
			} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			}		
		return array(
		'last_username' => $session->get(SecurityContext::LAST_USERNAME),
		'error'
		=> $error,
		);
	}
}
