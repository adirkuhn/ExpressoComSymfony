<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function MenuTopAction()
    {
        $menu = array();
        $menu[] = array( 'name' => 'Pagina Inicial' , 'icon' => 'MenuTop/agendamento.png' , 'href' => 'Home' );
        $menu[] = array( 'name' => 'Quadro de Avisos' , 'icon' => 'MenuTop/billboard.png' , 'href' => 'billboard' );
        $menu[] = array( 'name' => 'Correio' , 'icon' => 'MenuTop/mail.png' , 'href' => 'Mail' );
        $menu[] = array( 'name' => 'Chamados' , 'icon' => 'MenuTop/serviceCall.png' , 'href' => 'ServiceCall' );
        $menu[] = array( 'name' => 'Agendamento' , 'icon' => 'MenuTop/scheduling.png' , 'href' => 'Scheduling' );
        $menu[] = array( 'name' => 'Estatisticas' , 'icon' => 'MenuTop/statistically.png' , 'href' => 'Statistically' );
        $menu[] = array( 'name' => 'Suporte' , 'icon' => 'MenuTop/support.png' , 'href' => 'Support' );

        $response = new Response(json_encode($menu));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
