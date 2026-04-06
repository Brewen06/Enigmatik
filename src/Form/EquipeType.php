<?php

namespace App\Form;

use App\Entity\Avatar;
use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                null,
                [
                    'label' => 'Nom de l\'équipe',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'avatar',
                EntityType::class,
                [
                    'class' => Avatar::class,
                    'choice_label' => 'nom',
                    'choice_attr' => static function (?Avatar $avatar): array {
                        if (!$avatar) {
                            return [];
                        }

                        $imagePath = ltrim($avatar->getImage() ?? '', '/');
                        $imagePath = preg_replace('#^public/#', '', $imagePath) ?? $imagePath;

                        return [
                            'data-image' => $imagePath,
                        ];
                    },
                    'label' => 'Avatar de l\'équipe',
                    'placeholder' => 'Choisir un avatar',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-select'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
