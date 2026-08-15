<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TransactionType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('internalOrder', TextType::class, array(
                'required' => false,
                ))
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('number', TextType::class, array(
                'required' => false,
                ))
            ->add('amount', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('person', EntityType::class, array(
                'required' => false,
                'class' => Person::class,
                ))
            ->add('invoice', EntityType::class, array(
                'required' => false,
                'class' => Invoice::class,
                ))
            ->add('registrations', CollectionType::class, array(
                'required' => false,
                'entry_type' => RegistrationType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Transaction',
            'intention' => 'TransactionForm',
        ));
    }
}
