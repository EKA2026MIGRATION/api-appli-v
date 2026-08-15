<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Location;
use App\Entity\Registration;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PickupActivityType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registration', EntityType::class, array(
                'required' => false,
                'class' => Registration::class,
                ))
            ->add('date', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('start', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('end', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('statusChange', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('validated', TextType::class, array(
                'required' => false,
                ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('sport', EntityType::class, array(
                'required' => false,
                'class' => Sport::class,
                ))
            ->add('location', EntityType::class, array(
                'required' => false,
                'class' => Location::class,
                ))
            ->add('links', CollectionType::class, array(
                'required' => false,
                'entry_type' => PickupActivityGroupActivityLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PickupActivity',
            'intention' => 'PickupActivityForm',
        ));
    }
}
