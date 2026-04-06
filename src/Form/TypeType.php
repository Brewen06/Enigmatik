<?php

namespace App\Form;

use App\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'libelle',
                null,
                [
                    'label' => 'Libellé du type',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('imageUsage', ChoiceType::class, [
                'label' => 'Ce type nécessite-t-il des images ?',
                'choices' => [
                    'Non' => Type::IMAGE_USAGE_NONE,
                    'Oui' => Type::IMAGE_USAGE,
                ],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => static fn(): array => ['class' => 'form-check-input'],
                'row_attr' => [
                    'class' => 'mb-0'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Type::class,
        ]);
    }
}
