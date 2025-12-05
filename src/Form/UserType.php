<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse e-mail',
                    'class' => 'form-control'
                    ],
                
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Professeur' => 'ROLE_PROF',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => false,
                'label' => 'Rôles de l\'utilisateur',
                'attr' => [
                    'class' => 'form-check'
                ]
            ])
            ->add('password', null, [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
