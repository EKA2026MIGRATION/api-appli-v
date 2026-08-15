<?php

namespace App\Form;

use App\Entity\GroupActivity;
use App\Entity\Staff;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * GroupActivityStaffLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityStaffLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupActivity', EntityType::class, array(
                'required' => false,
                'class' => GroupActivity::class,
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
            'data_class' => 'App\Entity\PickupActivityGroupActivityLink',
            'intention' => 'PickupActivityGroupActivityLinkForm',
        ));
    }
}
