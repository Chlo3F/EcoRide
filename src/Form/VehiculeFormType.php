<?php

namespace App\Form;


use App\Entity\Vehicule;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', ChoiceType::class, [
                'choices' => [
                    'Audi' => 'Audi',
                    'BMW' => 'BMW',
                    'Citroën' => 'Citroën',
                    'Dacia' => 'Dacia',
                    'Fiat' => 'Fiat',
                    'Ford' => 'Ford',
                    'Honda' => 'Honda',
                    'Hyundai' => 'Hyundai',
                    'Kia' => 'Kia',
                    'Mercedes' => 'Mercedes',
                    'Nissan' => 'Nissan',
                    'Opel' => 'Opel',
                    'Peugeot' => 'Peugeot',
                    'Renault' => 'Renault',
                    'Seat' => 'Seat',
                    'Skoda' => 'Skoda',
                    'Tesla' => 'Tesla',
                    'Toyota' => 'Toyota',
                    'Volkswagen' => 'Volkswagen',
                    'Volvo' => 'Volvo',
                ],
                'placeholder' => 'Choisir une marque',
                'label' => 'Marque du véhicule',
            ])
            ->add('modele', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('couleur', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('immatriculation', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/',
                        'message' => 'Format attendu : XX-999-XX',
                    ])
                ]
            ])
            ->add('datePremiereImmat', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation',
            ])
            ->add('nbrPlaces', IntegerType::class, [
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\Range(['min' => 1, 'max' => 7]),
                ]
            ])
            ->add('electrique', CheckboxType::class, [
                'label' => 'Véhicule électrique ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}