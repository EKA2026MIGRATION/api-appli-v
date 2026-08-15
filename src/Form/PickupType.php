<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Registration;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PickupType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registration', EntityType::class, array(
                'required' => false,
                'class' => Registration::class,
                ))
            ->add('kind', TextType::class, array(
                'required' => false,
                ))
            ->add('start', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('phone', TextType::class, array(
                'required' => false,
                ))
            ->add('postal', TextType::class, array(
                'required' => false,
                ))
            ->add('address', TextType::class, array(
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
            ->add('sortOrder', IntegerType::class, array(
                'required' => false,
                ))
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('statusChange', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('places', IntegerType::class, array(
                'required' => false,
                ))
            ->add('comment', TextType::class, array(
                'required' => false,
                ))
            ->add('validated', TextType::class, array(
                'required' => false,
                ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('ride', EntityType::class, array(
                'required' => false,
                'class' => Ride::class,
                ))
            ->add('payment_due', TextType::class, array(
                'required' => false,
                ))
            ->add('payment_done', TextType::class, array(
                'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Pickup',
            'intention' => 'PickupForm',
        ));
    }
}
