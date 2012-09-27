<?php

namespace Expresso\ExpressoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Ldap Form Type.
 *
 * @author Adir Kuhn <adirkuhn@gmail.com>
 */
class LdapFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('host', 'text')
            ->add('port', 'text', array('required' => false))
            ->add('dn', 'text')
            ->add('user', 'text', array('required' => false))
            ->add('pass', 'repeated', array(
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
        return 'expressobundle_ldap_step';
    }
}