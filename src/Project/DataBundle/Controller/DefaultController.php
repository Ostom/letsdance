<?php

namespace Project\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Project\DataBundle\Entity\User;
class DefaultController extends Controller
{
    /**
     * @Route("/registration")
     * @Template()
     */
    public function registrationAction()
    {
		//$request = $this->getRequest();
		$user = new User();
		$user->setUsername('den');
		
		$factory = $this->get('security.encoder_factory');
		
		$encoder = $factory->getEncoder($user);
		$password = $encoder->encodePassword('111', $user->getSalt());
		$user->setPassword($password);
		
		$user->setRoles(array('ROLE_USER'));
		
		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($user);
		$em->flush();

        return array('user' => $user->getId());
    }
    
}
