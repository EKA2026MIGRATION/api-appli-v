<?php

namespace App\Form;
use App\Entity\Phone;
use App\Entity\Child;
use App\Entity\HistoricSms;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
class HistoricSmsListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TextType::class, array(
                'required' => false,
            ))
            ->add('phoneName', TextType::class, array(
                'required' => false,
            ))
            ->add('phone', EntityType::class, array(
                'required' => false,
                'class' => Phone::class,
            ))
            ->add('HistoricSms', EntityType::class, array(
                'required' => false,
                'class' => HistoricSms::class,
            ))
            ->add('timeSended', TimeType::class, array(
            'required' => false,
            'input' => 'datetime',
            'widget' => 'single_text',
            ))
            ->add('child', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('dateSended', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('content', TextType::class, array(
                'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\HistoricSmsList',
            'intention' => 'ExtractListForm',
        ));
    }
}
