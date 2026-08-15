<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;



/**
 * HistoricSmsType FormType
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricSmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeSended', TimeType::class, array(
            'required' => false,
            'input' => 'datetime',
            'widget' => 'single_text',
            ))
            ->add('dateSended', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('content', TextType::class, array(
                'required' => false,
            ))
            ->add('signature', TextType::class, array(
                'required' => false,
            ))
            ->add('name', TextType::class, array(
                'required' => false,
            ))
            ->add('status', TextType::class, array(
                'required' => false,
            ))
            ->add('unicode', TextType::class, array(
                'required' => false,
            ))
        ;   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\HistoricSms',
            'intention' => 'ExtractListForm',
        ));
    }
}
