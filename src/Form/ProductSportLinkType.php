<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ProductSportLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductSportLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, array(
                'required' => false,
                'class' => Product::class,
                ))
            ->add('sport', EntityType::class, array(
                'required' => false,
                'class' => Sport::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ProductSportLink',
            'intention' => 'ProductSportLinkForm',
        ));
    }
}
