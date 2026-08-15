<?php

namespace App\Form;

use App\Entity\Child;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChildChildLinkType FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildChildLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sibling', EntityType::class, array(
                'required' => false,
                'class' => Child::class,
                ))
            ->add('relation', TextType::class, array(
                'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ChildChildLink',
            'intention' => 'ChildChildLinkForm',
        ));
    }
}
