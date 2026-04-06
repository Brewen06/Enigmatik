<?php

namespace App\Form;

use App\Entity\Jeu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimerSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('timerMinutes', IntegerType::class, [
            'label' => 'Durée du chronomètre (minutes)',
            'help' => '0 pour désactiver le chronomètre.',
            'required' => false,
            'empty_data' => '0',
            'attr' => [
                'class' => 'form-control',
                'min' => 0,
                'step' => 1,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jeu::class,
        ]);
    }
}
