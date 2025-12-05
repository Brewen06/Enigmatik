<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Enigme;
use App\Entity\Vignette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EnigmeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ordre', 
                null, [
                    'label' => 'Ordre de l\'énigme dans le jeu',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
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
            ->add('codeSecret', 
                null, [
                    'label' => 'Code secret de l\'énigme',
                    'attr' => [
                        'class' => 'form-control'
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
            ->add('FileEnigme', FileType::class, [
                'label' => 'Fichier de l\'énigme (PDF, HTML, JSON, PHP, XML)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/php',
                            'application/html',
                            'application/json',
                            'application/xml',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide',
                    ])
                
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigme::class,
        ]);
    }
}
