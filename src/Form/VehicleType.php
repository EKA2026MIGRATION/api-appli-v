<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * VehicleType FormType.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => false,
                ))
            ->add('matriculation', TextType::class, array(
                'required' => false,
                ))
            ->add('combustible', TextType::class, array(
                'required' => false,
                ))
            ->add('places', IntegerType::class, array(
                'required' => false,
                ))
            ->add('mileage', TextType::class, array(
                'required' => false,
                ))
            ->add('photo', TextType::class, array(
                'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Vehicle',
            'intention' => 'VehicleForm',
        ));
    }
}
