<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AddressType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => false,
                ))
            ->add('address', TextType::class, array(
                'required' => false,
                ))
            ->add('address2', TextType::class, array(
                'required' => false,
                ))
            ->add('postal', TextType::class, array(
                'required' => false,
                ))
            ->add('town', TextType::class, array(
                'required' => false,
                ))
            ->add('country', TextType::class, array(
                'required' => false,
                ))
            ->add('latitude', NumberType::class, array(
                'required' => false,
                'scale' => 8,
                ))
            ->add('longitude', NumberType::class, array(
                'required' => false,
                'scale' => 8,
                ))
            ->add('links', CollectionType::class, array(
                'required' => false,
                'entry_type' => PersonAddressLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Address',
            'intention' => 'AddressForm',
        ));
    }
}
