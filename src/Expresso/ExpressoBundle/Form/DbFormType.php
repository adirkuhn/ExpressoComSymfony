<?php

namespace Expresso\ExpressoBundle\Form;

use Sensio\Bundle\DistributionBundle\Configurator\Step\DoctrineStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Database Form Type.
 *
 * @author Adir Kuhn <adirkuhn@gmail.com>
 */
class DbFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('driver', 'choice', array('choices' => DoctrineStep::getDrivers()))
            ->add('name', 'text')
            ->add('host', 'text')
            ->add('port', 'text', array('required' => false))
            ->add('user', 'text')
            ->add('password', 'repeated', array(
                'required'        => false,
                'type'            => 'password',
                'first_name'      => 'password',
                'second_name'     => 'password_again',
                'invalid_message' => 'The password fields must match.',
            ))
        ;
    }

     public function getName()
    {
        return 'expressobundle_db_step';
    }
}