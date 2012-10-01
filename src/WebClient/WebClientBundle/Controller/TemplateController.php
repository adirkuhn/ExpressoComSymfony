<?php

namespace WebClient\WebClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TemplateController extends Controller
{
    public function renderAction( $module , $template )
    {
        return $this->render('WebClientBundle:EJS:'.$module .'/'. $template.'.twig' );
    }
}
