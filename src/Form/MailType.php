<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MailType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subjectFr', TextType::class, array(
                'required' => false,
                ))
            ->add('contentFr', TextType::class, array(
                'required' => false,
                ))
            ->add('subjectEn', TextType::class, array(
                'required' => false,
                ))
            ->add('contentEn', TextType::class, array(
                'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Mail',
            'intention' => 'MailForm',
        ));
    }
}
