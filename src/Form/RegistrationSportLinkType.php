<?php

namespace App\Form;

use App\Entity\Registration;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RegistrationSportLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationSportLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registration', EntityType::class, array(
                'required' => false,
                'class' => Registration::class,
                ))
            ->add('sport', EntityType::class, array(
                'required' => false,
                'class' => Sport::class,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\RegistrationSportLink',
            'intention' => 'RegistrationSportLinkForm',
        ));
    }
}
