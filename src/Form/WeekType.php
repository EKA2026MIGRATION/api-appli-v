<?php

namespace App\Form;

use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * WeekType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class WeekType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', EntityType::class, array(
                'required' => false,
                'class' => Season::class,
                ))
            ->add('kind', TextType::class, array(
                'required' => false,
                ))
            ->add('groupName', TextType::class, array(
                'required' => false,
                ))
            ->add('code', TextType::class, array(
                'required' => false,
                ))
            ->add('name', TextType::class, array(
                'required' => false,
                ))
            ->add('dateStart', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Week',
            'intention' => 'WeekForm',
        ));
    }
}
