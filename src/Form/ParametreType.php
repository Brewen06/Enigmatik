<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Parametre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $existingChoices = $options['existing_choices'] ?? [];
        
        $builder
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nom',
                'label' => 'Jeu associé',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('libelle', null, [
                'label' => 'Nom du paramètre',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('reponseType', ChoiceType::class, [
                'label' => 'Type de réponse',
                'choices' => [
                    'Temporalité' => 'Mettre un : chrono, une date, etc.',
                    'Chiffre/Nombre' => 'Pour les règles et les scores : code_final, nombre de tentatives, etc.',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'data' => (\is_array($existingChoices) && $existingChoices !== []) ? 'temporalité' : 'chiffre/nombre',
            ])
            ->add('choix', TextareaType::class, [
                'label' => 'Valeurs choisis (chrono, code_final, etc.)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('valeur', IntegerType::class, [
                'label' => 'Valeur',
                'help' => 'Exemple: 60 pour une durée de 60 minutes',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 1,
                ],
            ]);

        $builder->get('choix')
            ->addModelTransformer(new CallbackTransformer(
                function ($choicesAsArray) {
                    return is_array($choicesAsArray) ? implode("\n", $choicesAsArray) : '';
                },
                function ($choicesAsString) {
                    return array_filter(array_map('trim', explode("\n", $choicesAsString)));
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parametre::class,
        ]);
    }
}
