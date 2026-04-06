<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Enigme;
use App\Entity\Vignette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('active', CheckboxType::class, [
                'label' => 'Énigme active (visible par les joueurs)',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label fw-bold focus'
                ],
                'row_attr' => [
                    'class' => 'form-check form-switch mb-3'
                ]
            ])
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
                'choice_attr' => static function (?Type $type): array {
                    if ($type === null) {
                        return [];
                    }

                    return [
                        'data-image-usage' => $type->getImageUsage(),
                    ];
                },
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
                'indice',
                null,
                [
                    'label' => 'Indice',
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
            ])
            ->add('frisePayload', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'empty_data' => '',
            ])
            ->add('yearStart', null, [
                'label' => 'Année de début',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1000',
                    'max' => '2100',
                    'type' => 'number'
                ]
            ])
            ->add('yearEnd', null, [
                'label' => 'Année de fin',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1000',
                    'max' => '2100',
                    'type' => 'number'
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

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            /** @var Enigme $enigme */
            $enigme = $event->getData();
            $form = $event->getForm();
            $reponseType = (string) ($form->get('reponseType')->getData() ?? 'libre');

            $normalizedSolutions = $this->normalizeLines((string) ($enigme->getSolution() ?? ''));
            $type = $enigme->getType();
            $isFriseType = $type !== null && mb_strtolower((string) $type->getLibelle()) === 'frise';

            if ($isFriseType) {
                $friseOrder = $this->normalizeFrisePayload((string) ($form->get('frisePayload')->getData() ?? ''));

                if (count($friseOrder) < 2) {
                    $form->get('type')->addError(new FormError('Ajoutez au moins 2 images dans la frise.'));
                    return;
                }

                $indice = trim((string) ($enigme->getIndice() ?? ''));
                if ($indice === '') {
                    $form->get('indice')->addError(new FormError('Veuillez renseigner l\'indice du code final pour la frise.'));
                    return;
                }

                $friseItems = [];
                foreach ($friseOrder as $index => $vignetteId) {
                    $friseItems[] = [
                        'vignetteId' => $vignetteId,
                        'position' => $index + 1,
                    ];
                }

                $enigme->setFriseItems($friseItems);
                $enigme->setChoices([]);
                if ($normalizedSolutions === []) {
                    $enigme->setSolution('frise');
                }
                return;
            }

            $enigme->setFriseItems([]);

            if ($normalizedSolutions === []) {
                $form->get('solution')->addError(new FormError('Veuillez renseigner au moins une solution.'));
            }

            if ($type !== null && $type->requiresImage()) {
                $hasMedia = (string) ($enigme->getLien() ?? '') !== '' || $enigme->getVignette() !== null;

                if (!$hasMedia) {
                    $form->get('lien')->addError(new FormError('Ce type d\'énigme nécessite un support image (lien ou vignette).'));
                }
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var Enigme|null $enigme */
            $enigme = $event->getData();
            $form = $event->getForm();

            if ($enigme === null || $enigme->getId() === null) {
                return;
            }

            $choices = $enigme->getChoices();
            if (is_array($choices) && count($choices) > 0) {
                $form->get('reponseType')->setData('multiple');
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

    /**
     * @return list<int>
     */
    private function normalizeFrisePayload(string $payload): array
    {
        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_unique(array_map('intval', $decoded)), static fn(int $id): bool => $id > 0));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigme::class,
        ]);
    }
}
