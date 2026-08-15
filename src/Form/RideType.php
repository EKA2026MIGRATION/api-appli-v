<?php

namespace App\Form;

use App\Entity\Ride;
use App\Entity\Staff;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RideType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locked', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('kind', TextType::class, array(
                'required' => false,
                ))
            ->add('linkedRide', EntityType::class, array(
                'required' => false,
                'class' => Ride::class,
                ))
            ->add('date', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('name', TextType::class, array(
                'required' => false,
                ))
            ->add('places', IntegerType::class, array(
                'required' => false,
                ))
            ->add('start', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('arrival', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('report', TextType::class, array(
                'required' => false,
                ))
            ->add('startPoint', TextType::class, array(
                'required' => false,
                ))
            ->add('endPoint', TextType::class, array(
                'required' => false,
                ))
            ->add('staff', EntityType::class, array(
                'required' => false,
                'class' => Staff::class,
                ))
            ->add('vehicle', EntityType::class, array(
                'required' => false,
                'class' => Vehicle::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Ride',
            'intention' => 'RideForm',
        ));
    }
}
