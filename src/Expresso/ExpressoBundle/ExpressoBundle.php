<?php

namespace Expresso\ExpressoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Expresso\ExpressoBundle\ConfigStep\DBStep;
use Expresso\ExpressoBundle\ConfigStep\LdapStep;
use Expresso\ExpressoBundle\ConfigStep\EmailStep;

class ExpressoBundle extends Bundle
{
    public function boot()
    {
        if ( $this->container->get('kernel')->getEnvironment() === 'dev' ) {
            //getting configurator service and parameters
            $configurator = $this->container->get('sensio.distribution.webconfigurator');
            $parameters = $configurator->getParameters();

            //check if the config file (parameters.ini) has all keys
            //TODO: needs to implements individual server check and 'connection' test
           
            //create config steps
            $configurator->addStep(new DBStep($parameters));
            $configurator->addStep(new LdapStep($parameters));
            $configurator->addStep(new EmailStep($parameters));
        }
    }
}
