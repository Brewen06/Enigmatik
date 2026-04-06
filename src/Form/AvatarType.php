<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', 
                null, [
                    'label' => 'Nom de l\'avatar',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la vignette (Fichier image)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (SVG)',
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
            'data_class' => Avatar::class,
        ]);
    }
}
