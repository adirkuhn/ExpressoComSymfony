<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function MenuTopAction()
    {
        $menu = array();
        $menu[] = array( 'title' => 'Página Inicial' , 'name' => 'Home' );
        $menu[] = array( 'title' => 'Quadro de Avisos' , 'name' => 'Billboard' );
        $menu[] = array( 'title' => 'Correio' , 'name' => 'Mail' );
        $menu[] = array( 'title' => 'Chamados' , 'name' => 'ServiceCall' );
        $menu[] = array( 'title' => 'Agendamento' , 'name' => 'Scheduling' );
        $menu[] = array( 'title' => 'Estatisticas' , 'name' => 'Statistically' );
        $menu[] = array( 'title' => 'Suporte' , 'name' => 'Support' );

        $response = new Response(json_encode($menu));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
