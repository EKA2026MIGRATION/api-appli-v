<?php

namespace App\Form;

use App\Entity\PickupActivity;
use App\Entity\GroupActivity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PickupActivityGroupActivityLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityGroupActivityLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pickupActivity', EntityType::class, array(
                'required' => false,
                'class' => PickupActivity::class,
                ))
            ->add('groupActivity', EntityType::class, array(
                'required' => false,
                'class' => GroupActivity::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PickupActivityGroupActivityLink',
            'intention' => 'PickupActivityGroupActivityLinkForm',
        ));
    }
}
