<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Location;
use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RegistrationType FormType.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registration', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('person', EntityType::class, array(
                'required' => false,
                'class' => Person::class,
                ))
            ->add('address', EntityType::class, array(
                'required' => false,
                'class' => Address::class,
                ))
            ->add('product', EntityType::class, array(
                'required' => false,
                'class' => Product::class,
                ))
            ->add('invoice', IntegerType::class, array(
                'required' => false,
                ))
            ->add('payed', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('preferences', TextType::class, array(
                'required' => false,
                ))
            ->add('sessions', TextType::class, array(
                'required' => false,
                ))
            ->add('location', EntityType::class, array(
                'required' => false,
                'class' => Location::class,
                ))
            ->add('sports', CollectionType::class, array(
                'required' => false,
                'entry_type' => RegistrationSportLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('transaction', EntityType::class, array(
                'required' => false,
                'class' => Transaction::class,
            ))
        
            ->add('hasLunch', TextType::class, array(
                'required' => false,
                ))
            ->add('hasTransport', TextType::class, array(
                'required' => false,
                ))
           
           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Registration',
            'intention' => 'RegistrationForm',
        ));
    }
}
