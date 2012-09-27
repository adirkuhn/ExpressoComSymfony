<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SymfonyRequirements;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{

    /**
    * Telas de cnfiguração básica do Expresso
    */
    //TODO: traduzir mensagens em inglês
    //TODO: aplicar layout nas telas
    public function setupAction($index = 0)
    {
        if (!$this->checkDevEnvironment()) {
            return $this->container->get('templating')->renderResponse('ExpressoBundle:Setup:deny.html.twig');
        }

        $configurator = $this->container->get('sensio.distribution.webconfigurator');

        //Como este seviço de configuração é do symfony, ele adiciona suas configurações
        //de bando de dados e secret, então é necessário remover eles do valor do count
        $stepCount = $configurator->getStepCount() - 2;

        $step = $configurator->getStep($index);
        $form = $this->container->get('form.factory')->create($step->getFormType(), $step);

        $request = $this->container->get('request');
        if ('POST' === $request->getMethod()) { 
            $form->bindRequest($request);
            if ($form->isValid()) {
                $configurator->mergeParameters($step->update($form->getData()));
                $configurator->write();

                $index++;

                if ($index < $stepCount) {
                    return new RedirectResponse($this->container->get('router')->generate('ExpressoBundle_setup', array('index' => $index)));
                }

                return new RedirectResponse($this->container->get('router')->generate('ExpressoBundle_final'));
            }
        }


        return $this->container->get('templating')->renderResponse($step->getTemplate(), array(
            'form'    => $form->createView(),
            'index'   => $index,
            'count'   => $stepCount,
            'version' => $this->getVersion(),
        ));
    }

    public function finalAction()
    {
        if (!$this->checkDevEnvironment()) {
            return $this->container->get('templating')->renderResponse('ExpressoBundle:Setup:deny.html.twig');
        }

        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $configurator->clean();

        try {
            $welcomeUrl = $this->container->get('router')->generate('_welcome');
        } catch (\Exception $e) {
            $welcomeUrl = null;
        }

        return $this->container->get('templating')->renderResponse('ExpressoBundle:Setup:final.html.twig', array(
            'welcome_url' => $welcomeUrl,
            'parameters'  => $configurator->render(),
            'yml_path'    => $this->container->getParameter('kernel.root_dir').'/config/parameters.yml',
            'is_writable' => $configurator->isFileWritable(),
            'version'     => $this->getVersion(),
        ));
    }

    /**
    * Verifica se esta no ambiente de desenvolvimento
    * @return <boolean>
    */
    public function checkDevEnvironment()
    {
        return ( $this->container->get('kernel')->getEnvironment() === 'dev' );
    }

    public function getVersion()
    {
        $kernel = $this->container->get('kernel');

        return $kernel::VERSION;
    }
}
