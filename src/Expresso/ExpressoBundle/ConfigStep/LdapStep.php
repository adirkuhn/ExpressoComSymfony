<?php

namespace Expresso\ExpressoBundle\ConfigStep;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;
use Expresso\ExpressoBundle\Form\LdapFormType;

/**
 * Configuração do LDAP.
 *
 * @author Adir Kuhn <adirkuhn@gmail.com>
 */
class LdapStep implements StepInterface
{
    public $host;

    public $port;

    public $dn;

    public $user;

    public $pass;

    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (0 === strpos($key, 'ldap_')) {
                $parameters[substr($key, 5)] = $value;
                $key = substr($key, 5);
                $this->$key = $value;
            }
        }
    }

    private function generateRandomSecret()
    {
        return hash('sha1', uniqid(mt_rand()));
    }

    /**
     * @see StepInterface
     */
    public function getFormType()
    {
        return new LdapFormType();
    }

    /**
     * @see StepInterface
     */
    public function checkRequirements()
    {
        return array();
    }

    /**
     * checkOptionalSettings
     */
    public function checkOptionalSettings()
    {
        return array();
    }

    /**
     * @see StepInterface
     */
    public function update(StepInterface $data)
    {
        $parameters = array();

        foreach ($data as $key => $value) {
            $parameters['ldap_'.$key] = $value;
        }

        return $parameters;
    }

    /**
     * @see StepInterface
     */
    public function getTemplate()
    {
        return 'ExpressoBundle:Setup:ldap.html.twig';
    }
}
