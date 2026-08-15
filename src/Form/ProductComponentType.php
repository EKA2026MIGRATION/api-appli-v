<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ProductComponentType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductComponentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, array(
                'required' => false,
                'class' => Product::class,
                ))
            ->add('nameFr', TextType::class, array(
                'required' => false,
                ))
            ->add('nameEn', TextType::class, array(
                'required' => false,
                ))
            ->add('priceHt', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('quantity', IntegerType::class, array(
                'required' => false,
                ))
            ->add('vat', NumberType::class, array(
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ProductComponent',
            'intention' => 'ProductComponentForm',
        ));
    }
}
