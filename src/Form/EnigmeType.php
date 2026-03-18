<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Enigme;
use App\Entity\Vignette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class EnigmeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $existingChoices = $options['data']?->getChoices();
        $builder
            ->add('ordre', null, [
                'label' => 'Identifiant de l\'énigme (ordre de passage)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add(
                'titre',
                null,
                [
                    'label' => 'Titre de l\'énigme',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'consigne',
                null,
                [
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
            ->add(
                'codeSecret',
                null,
                [
                    'label' => 'Code secret de l\'énigme',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('lien', null, [
                'label' => 'Lien associé à l\'énigme (image, vidéo, audio, etc...)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('reponseType', ChoiceType::class, [
                'label' => 'Type de réponse',
                'choices' => [
                    'Réponse libre' => 'libre',
                    'Réponse à choix (multiple)' => 'multiple',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'data' => (\is_array($existingChoices) && $existingChoices !== []) ? 'multiple' : 'libre',
            ])
            ->add('choices', TextareaType::class, [
                'label' => 'Réponses possibles (une par ligne)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('solution', null, [
                'label' => 'Solution de l\'énigme',
                'attr' => [
                    'class' => 'form-control'
                ]
                
            ]);

        $builder->get('choices')
        ->addModelTransformer(new CallbackTransformer(
            function ($choicesAsArray) {
                if (!\is_array($choicesAsArray) || $choicesAsArray === []) {
                    return '';
                }
                return implode("\n", $choicesAsArray);
            },
            function ($choicesAsString) {
                if ($choicesAsString === null || trim((string) $choicesAsString) === '') {
                    return [];
                }
                return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $choicesAsString))));
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
