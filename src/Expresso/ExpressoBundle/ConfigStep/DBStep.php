<?php

namespace Expresso\ExpressoBundle\ConfigStep;

use Sensio\Bundle\DistributionBundle\Configurator\Step\DoctrineStep;
use Expresso\ExpressoBundle\Form\DbFormType;

class DBStep extends DoctrineStep
{

    /**
     * @see StepInterface
     */
    public function getFormType()
    {
        return new DbFormType();
    }

    /**
    *
    */
    public function getTemplate()
    {
        return 'ExpressoBundle:Setup:db.html.twig';
    }
}