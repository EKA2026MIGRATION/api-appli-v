<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Staff;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChildType FormType.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'required' => false,
                ))
            ->add('gender', TextType::class, array(
                'required' => false,
                ))
            ->add('lastname', TextType::class, array(
                'required' => false,
                ))
            ->add('phone', TextType::class, array(
                'required' => false,
                ))
            ->add('birthdate', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
            ->add('medical', TextType::class, array(
                'required' => false,
                ))
            ->add('photo', TextType::class, array(
                'required' => false,
                ))
            ->add('school', EntityType::class, array(
                'required' => false,
                'class' => School::class,
                ))
            ->add('france_resident', TextType::class, array(
                'required' => false,
                ))
            ->add('comment', TextType::class, array(
                'required' => false,
                ))
            ->add('pickup_instruction', TextType::class, array(
                'required' => false,
                ))
            ->add('medicalCertificate', TextType::class, array(
                'required' => false,
                ))
            ->add('sportifProfil', TextType::class, array(
                'required' => false,
                ))
            ->add('childHand', TextType::class, array(
                'required' => false,
                ))
            ->add('childFoot', TextType::class, array(
                'required' => false,
                ))
            ->add('guidingEye', TextType::class, array(
                'required' => false,
                ))
            ->add('franceResident', CheckboxType::class, array(
                'required' => false,
                ))
            ->add('links', CollectionType::class, array(
                'required' => false,
                'entry_type' => ChildPersonLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('siblings', CollectionType::class, array(
                'required' => false,
                'entry_type' => ChildChildLinkType::class,
                'mapped' => false,
                'allow_extra_fields' => true,
                ))
            ->add('staff', EntityType::class, array(
                'required' => false,
                'class' => Staff::class,
                ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Child',
            'intention' => 'ChildForm',
        ));
    }
}
