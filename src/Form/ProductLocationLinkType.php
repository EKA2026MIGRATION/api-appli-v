<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ProductLocationLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductLocationLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, array(
                'required' => false,
                'class' => Product::class,
                ))
            ->add('location', EntityType::class, array(
                'required' => false,
                'class' => Location::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ProductLocationLink',
            'intention' => 'ProductLocationLinkForm',
        ));
    }
}
