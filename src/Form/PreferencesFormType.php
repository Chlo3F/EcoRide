<?php

namespace App\Form;

use App\Entity\Preferences;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferencesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fumeur', CheckboxType::class, [
                'label' => 'Fumeur',
                'required' => false,
            ])
            ->add('animaux', CheckboxType::class, [
                'label' => 'Animaux acceptés',
                'required' => false,
            ])
            ->add('autre', TextareaType::class, [
                'label' => 'Autres préférences',
                'required' => false,
                'empty_data' => '',
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preferences::class,
        ]);
    }
}
