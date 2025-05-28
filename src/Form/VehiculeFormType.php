<?php

namespace App\Form;


use App\Entity\Vehicule;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class)
            ->add('modele', TextType::class)
            ->add('couleur', TextType::class)
            ->add('immatriculation', TextType::class)
            ->add('datePremiereImmat', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation',
            ])
            ->add('nbrPlaces', IntegerType::class, [
                'label' => 'Nombre de places',
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