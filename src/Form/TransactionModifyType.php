<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TransactionModifyType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TransactionModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', TextType::class, array(
                'required' => false,
                ))
            ->add('invoice', EntityType::class, array(
                'required' => false,
                'class' => Invoice::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Transaction',
            'intention' => 'TransactionModifyForm',
        ));
    }
}
