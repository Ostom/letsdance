<?php

namespace Project\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\DataBundle\Entity\User;
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
		if(isset($_REQUEST['_username']))
		{
			$request = Request::createFromGlobals();		
			$request->getPathInfo();		
			$name = $request->request->get('_username');
			$pass = $request->request->get('_password');
					
			$a = new FormCheck();
			//file
			
			$a->test();
			//$request = $this->getRequest();
			$user = new User();
			
			if(isset($_FILE['filename']))
				$user->setImg($a->uploadFile());
			else{ $user->setImg(getcwd().'/letsdance/static/files/'.'default.png'); }
			 
			$user->setUsername($name);
			
			$factory = $this->get('security.encoder_factory');
			
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($pass, $user->getSalt());
			$user->setPassword($password);
			
			$user->setRoles(array('ROLE_USER'));
			
			echo($user->getUsername());
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($user);
			$em->flush();
		}
        return array('name' => $name);
    }
    
}
