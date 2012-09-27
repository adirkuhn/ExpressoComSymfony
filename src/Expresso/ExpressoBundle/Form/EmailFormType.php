<?php

namespace Expresso\ExpressoBundle\Form;

use Expresso\ExpressoBundle\ConfigStep\EmailStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Email Form Type.
 *
 * @author Adir Kuhn <adirkuhn@gmail.com>
 */
class EmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*defaul config*/
            ->add('name', 'text')
            ->add('default_domain', 'text')
            ->add('organization')

            /*smtp config*/
            ->add('smtp_type', 'choice', array('choices' => EmailStep::getSmtpType()))
            ->add('smtp_host', 'text')
            ->add('smtp_port', 'text', array('required' => false))

            /* imap/pop3 */
            // ->add('imap_type', 'choice', array('choices' => EmailStep::getImapType()))
            ->add('imap_host', 'text')
            ->add('imap_port', 'text')
            ->add('imap_delimiter', 'choice', array('choices' => array('.', '/')))

        ;
    }

     public function getName()
    {
        return 'expressobundle_email_step';
    }
}