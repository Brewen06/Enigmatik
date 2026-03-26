<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Parametre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $existingChoices = $options['existing_choices'] ?? [];

        $builder
            ->add('reponseType', ChoiceType::class, [
                'label' => 'Type de paramètre',
                'choices' => [
                    'Système (Règles globales, textes, toggles...)' => 'systeme',
                    'Numérique - Temps (Minuteur, pénalités...)' => 'temps',
                    'Numérique - Nombre (Scores, essais...)' => 'nombre',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'data' => 'systeme',
                'attr' => ['class' => 'mb-3 parametre-type-selector']
            ])
            ->add('libelle', null, [
                'label' => 'Nom du paramètre',
                'help' => 'Exemples : Max tentatives, Pénalité erreur, Afficher chronomètre, etc.',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le nom du paramètre...'
                ]
            ])
            ->add('valeur', null, [
                'label' => 'Valeur par défaut',
                'help' => 'Exemple: 60 (pour un temps), 3 (pour des essais), oui/non (pour une option)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('choix', TextareaType::class, [
                'label' => 'Paramètres avancés / Options multiples (Optionnel)',
                'help' => 'Si besoin de plusieurs valeurs, entrez une valeur par ligne. Laissez vide sinon.',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => "Option 1\nOption 2\n..."
                ],
            ])
        ;

        $builder->get('choix')
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
            'data_class' => Parametre::class,
        ]);
    }
}
