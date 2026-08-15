<?php

namespace App\Form;

use App\Entity\Child;
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
 * InvoiceType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('person', EntityType::class, array(
                'required' => false,
                'class' => Person::class,
                ))
            ->add('nameFr', TextType::class, array(
                'required' => false,
                ))
            ->add('nameEn', TextType::class, array(
                'required' => false,
                ))
            ->add('descriptionFr', TextType::class, array(
                'required' => false,
                ))
            ->add('descriptionEn', TextType::class, array(
                'required' => false,
                ))
            ->add('date', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('number', TextType::class, array(
                'required' => false,
                ))
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('paymentMethod', TextType::class, array(
                'required' => false,
                ))
            ->add('priceTtc', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('prices', TextType::class, array(
                'required' => false,
                ))
            ->add('address', TextType::class, array(
                'required' => false,
                ))
            ->add('postal', TextType::class, array(
                'required' => false,
                ))
            ->add('town', TextType::class, array(
                'required' => false,
                ))
            ->add('invoiceProducts', CollectionType::class, array(
                'required' => false,
                'entry_type' => InvoiceProductType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Invoice',
            'intention' => 'InvoiceForm',
        ));
    }
}
