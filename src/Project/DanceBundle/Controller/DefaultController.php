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
        
		$cookies = $this->container->get('security.context')->getToken()->getUser();
		
        return array('cookies' => $cookies);//get(SecurityContext::LAST_USERNAME));
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
