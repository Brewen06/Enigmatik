<?php

namespace App\Form;

use App\Entity\Jeu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JeuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'titre',
                null,
                [
                    'label' => 'Titre du jeu',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'messageDeBienvenue',
                null,
                [
                    'label' => 'Message de bienvenue',
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5
                    ]
                ]
            )
            ->add(
                'imageBienvenue',
                null,
                [
                    'label' => 'Image de bienvenue',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'codeFinal',
                null,
                [
                    'label' => 'Code final pour gagner',
                    'help' => 'Ce code devra être déduit par les joueurs grâce aux indices récoltés.',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Ex : BRAVO2026'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jeu::class,
        ]);
    }
}
