<?php

namespace App\Form;


use App\Entity\Trajet;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TrajetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On récupère l'utilisateur passé via les options
        $user = $options['user'];

        $builder
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choices' => $user->getVehicules(),
                'choice_label' => function ($vehicule) {
                    return $vehicule->getMarque() . ' ' . $vehicule->getModele();
                },
            ])
            ->add('villeDepart', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('villeArrivee', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('dateHeureDepart', DateTimeType::class, [
                'widget' => 'single_text',
            ])

            ->add('placesDisponibles', IntegerType::class, [
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\Range(['min' => 1, 'max' => 7]),
                ]
            ])
            ->add('credits', IntegerType::class)
            ->add('energieElectrique', null, [
                'label' => 'Trajet en véhicule électrique ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
            'user' => null,
        ]);
    }
}