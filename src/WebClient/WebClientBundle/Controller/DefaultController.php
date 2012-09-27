<?php

namespace WebClient\WebClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebClientBundle:Default:index.html.twig' );
    }

    public function loginAction()
    {
        return $this->render('WebClientBundle:Default:login.html.twig');
    }
}
