<?php

namespace App\Form;

use App\Entity\InvoiceProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * InvoiceComponentType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceComponentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoiceProduct', EntityType::class, array(
                'required' => false,
                'class' => InvoiceProduct::class,
                ))
            ->add('nameFr', TextType::class, array(
                'required' => false,
                ))
            ->add('nameEn', TextType::class, array(
                'required' => false,
                ))
            ->add('vat', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('priceHt', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('priceVat', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('priceTtc', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('quantity', IntegerType::class, array(
                'required' => false,
                ))
            ->add('totalHt', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('totalVat', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('totalTtc', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\InvoiceComponent',
            'intention' => 'InvoiceComponentForm',
        ));
    }
}
