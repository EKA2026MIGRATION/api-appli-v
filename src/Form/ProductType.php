<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Family;
use App\Entity\Mail;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ProductType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('family', EntityType::class, array(
                'required' => false,
                'class' => Family::class,
                ))
            ->add('season', EntityType::class, array(
                'required' => false,
                'class' => Season::class,
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
            ->add('priceTtc', NumberType::class, array(
                'required' => false,
                'scale' => 2,
                ))
            ->add('transport', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('lunch', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('photo', TextType::class, array(
                'required' => false,
                ))
            ->add('mail', EntityType::class, array(
                'required' => false,
                'class' => Mail::class,
                ))
            ->add('isLocationSelectable', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('isOffered', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('isDateSelectable', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('isHourSelectable', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('isSportSelectable', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('visibility', TextType::class, array(
                'required' => false,
                ))
            ->add('hourDropin', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('hourDropoff', TimeType::class, array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                ))
            ->add('categories', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductCategoryLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('components', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductComponentType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('dates', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductDateLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('hours', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductHourLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('locations', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductLocationLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('sports', CollectionType::class, array(
                'required' => false,
                'entry_type' => ProductSportLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Product',
            'intention' => 'ProductForm',
        ));
    }
}
