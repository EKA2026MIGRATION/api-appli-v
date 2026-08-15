<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * PersonType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'required' => false,
                ))
            ->add('lastname', TextType::class, array(
                'required' => false,
                ))
            ->add('photo', TextType::class, array(
                'required' => false,
                ))
            ->add('birthdate', DateType::class, array(
                'required' => false,
                'widget' => 'single_text'
                ))
            ->add('identifier', TextType::class, array(
                'required' => false,
                ))
            ->add('relations', CollectionType::class, array(
                'required' => false,
                'entry_type' => PersonPersonLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Person',
            'intention' => 'PersonForm',
            'allow_extra_fields' => true,
        ));
    }
}
