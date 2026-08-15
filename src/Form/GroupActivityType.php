<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * GroupActivityType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('name', TextType::class, array(
                'required' => false,
                ))
            ->add('age', TextType::class, array(
                'required' => false,
                ))
            ->add('locked', CheckboxType::class, array(
                'required' => false,
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
            ->add('lunch', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('comment', TextType::class, array(
                'required' => false,
                ))
            ->add('location', EntityType::class, array(
                'required' => false,
                'class' => Location::class,
                ))
            ->add('area', TextType::class, array(
                'required' => false,
                ))
            ->add('sport', EntityType::class, array(
                'required' => false,
                'class' => Sport::class,
                ))
            ->add('links', CollectionType::class, array(
                'required' => false,
                'entry_type' => PickupActivityGroupActivityLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('staff', CollectionType::class, array(
                'required' => false,
                'entry_type' => GroupActivityStaffLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\GroupActivity',
            'intention' => 'GroupActivityForm',
        ));
    }
}
