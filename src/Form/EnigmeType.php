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
            ->add('ordre')
            ->add('titre')
            ->add('consigne')
            ->add('codeSecret')
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'nom', 
                ])
            ->add('vignette', EntityType::class, [
                'class' => Vignette::class,
                'choice_label' => 'information',
                'label' => 'Vignette associée',
                'placeholder' => 'Choisir une vignette',
                'required' => false,
                
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
