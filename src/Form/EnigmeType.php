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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EnigmeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $existingChoices = $options['data']?->getChoices();
        $builder
            ->add('ordre', null, [
                'label' => 'Ordre de passage',
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
            ->add('solution', TextareaType::class, [
                'label' => 'Solution de l\'énigme',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => "Exemple (unique): HTTPS\n\nExemple (multiple):\nréponse 1\nréponse 2"
                ],
                'empty_data' => ''
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

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            /** @var Enigme $enigme */
            $enigme = $event->getData();
            $form = $event->getForm();
            $reponseType = (string) ($form->get('reponseType')->getData() ?? 'libre');

            $normalizedSolutions = $this->normalizeLines((string) ($enigme->getSolution() ?? ''));
            if ($normalizedSolutions === []) {
                $form->get('solution')->addError(new FormError('Veuillez renseigner au moins une solution.'));
            }

            if ($reponseType === 'libre') {
                $enigme->setChoices([]);

                return;
            }

            $choices = $enigme->getChoices() ?? [];
            $normalizedChoices = $this->normalizeArrayValues($choices);

            if ($normalizedChoices === []) {
                $form->get('choices')->addError(new FormError('Veuillez renseigner des reponses possibles pour un quiz.'));

                return;
            }

            foreach ($normalizedSolutions as $solution) {
                if (!in_array($solution, $normalizedChoices, true)) {
                    $form->get('solution')->addError(new FormError('Chaque solution doit correspondre a une reponse possible.'));
                    break;
                }
            }
        });
    }

    /**
     * @return list<string>
     */
    private function normalizeLines(string $value): array
    {
        $parts = preg_split('/\R+/', $value) ?: [];

        return $this->normalizeArrayValues($parts);
    }

    /**
     * @param array<mixed> $values
     *
     * @return list<string>
     */
    private function normalizeArrayValues(array $values): array
    {
        $normalized = array_map(function ($value): string {
            $line = trim((string) $value);
            $line = preg_replace('/\s+/', ' ', $line) ?? $line;

            return mb_strtolower($line);
        }, $values);

        return array_values(array_unique(array_filter($normalized, fn(string $line): bool => $line !== '')));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigme::class,
        ]);
    }
}
