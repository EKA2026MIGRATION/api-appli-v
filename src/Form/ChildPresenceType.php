<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Location;
use App\Entity\Person;
use App\Entity\Registration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChildPresenceType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildPresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registration', EntityType::class, array(
                'required' => false,
                'class' => Registration::class,
                ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('person', EntityType::class, array(
                'required' => false,
                'class' => Person::class,
                ))
            ->add('location', EntityType::class, array(
                'required' => false,
                'class' => Location::class,
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
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('statusChange', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('end', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ChildPresence',
            'intention' => 'ChildPresenceForm',
        ));
    }
}
