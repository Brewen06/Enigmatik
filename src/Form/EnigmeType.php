<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Enigme;
use App\Entity\Vignette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\CallbackTransformer;

class EnigmeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', 
                null, [
                    'label' => 'Titre de l\'énigme',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('consigne', 
                null, [
                    'label' => 'Consigne de l\'énigme',
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5
                    ]
                ]
            )
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'libelle',
                'label' => 'Type d\'énigme',
                'placeholder' => 'Choisir un type d\'énigme',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ] 
                ])
            ->add('vignette', EntityType::class, [
                'class' => Vignette::class,
                'choice_label' => 'information',
                'label' => 'Vignette associée',
                'placeholder' => 'Choisir une vignette',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('codeSecret', 
                null, [
                    'label' => 'Code secret de l\'énigme',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('choices', TextareaType::class, [
                'label' => 'Choix de réponses (une par ligne, laisser vide pour réponse libre)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'help' => 'Si rempli, l\'énigme sera présentée sous forme de QCM.'
            ])
        ;

        $builder->get('choices')
            ->addModelTransformer(new CallbackTransformer(
                function ($choicesAsArray) {
                    // transform the array to a string
                    if (!$choicesAsArray) {
                        return '';
                    }
                    return implode("\n", $choicesAsArray);
                },
                function ($choicesAsString) {
                    // transform the string back to an array
                    if (!$choicesAsString) {
                        return [];
                    }
                    return array_filter(array_map('trim', explode("\n", $choicesAsString)));
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigme::class,
        ]);
    }
}
