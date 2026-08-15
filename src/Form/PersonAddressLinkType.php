<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PersonAddressLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonAddressLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', EntityType::class, array(
                'required' => false,
                'class' => Person::class,
                ))
            ->add('address', EntityType::class, array(
                'required' => false,
                'class' => Address::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PersonAddressLink',
            'intention' => 'PersonAddressLinkForm',
        ));
    }
}
