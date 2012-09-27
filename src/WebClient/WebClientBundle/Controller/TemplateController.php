<?php

namespace WebClient\WebClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TemplateController extends Controller
{
    public function renderAction( $template )
    {
        return $this->render('WebClientBundle:EJS:'.$template.'.twig' );
    }
}
