<?php

namespace Expresso\ExpressoBundle\ConfigStep;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;
use Expresso\ExpressoBundle\Form\EmailFormType;

/**
 * Configuração de Email.
 *
 * @author Adir Kuhn <adirkuhn@gmail.com>
 */
class EmailStep implements StepInterface
{
    public $name;
    public $default_domain;
    public $organization;

    public $smtp_type;
    public $smtp_host;
    public $smtp_port;

    public $imap_type;
    public $imap_host;
    public $imap_port;
    public $imap_delimiter;
    
    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (0 === strpos($key, 'email_')) {
                $parameters[substr($key, 5)] = $value;
                $key = substr($key, 5);
                $this->$key = $value;
            }
        }
    }

    /**
     * @see StepInterface
     */
    public function getFormType()
    {
        return new EmailFormType();
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
            $parameters['email_'.$key] = $value;
        }

        return $parameters;
    }

    /**
     * @see StepInterface
     */
    public function getTemplate()
    {
        return 'ExpressoBundle:Setup:email.html.twig';
    }

    static public function getSmtpType()
    {
        return array(
            'smtp_default' => 'SMTP Padrão',
            'smtp_postfix' => 'SMTP com Postfix'
        );
    }
}
