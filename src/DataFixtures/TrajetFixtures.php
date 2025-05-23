<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trajet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrajetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
        throw new \Exception('Aucun utilisateur trouvé pour assigner les trajets. Exécute d’abord UserFixtures.');
        }

        $villes = ['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Toulouse', 'Lille', 'Nice'];
        $tempsTrajets = ['Paris-Lyon' => 6, 'Paris-Bordeaux' => 6,'Paris-Marseille' => 8, 
                         'Paris-Toulouse' => 7, 'Paris-Nice' => 10, 'Paris-Lille' => 3,
                         'Lyon-Nice' => 4, 'Lyon-Marseille' => 4, 'Lyon-Toulouse' => 5, 
                         'Lyon-Bordeaux' => 6, 'Lyon-Lille' => 6,'Bordeaux-Toulouse' => 3,
                         'Bordeaux-Marseille' => 7, 'Bordeaux-Nice' => 8, 'Bordeaux-Lille' => 8,
                         'Toulouse-Nice' => 5, 'Toulouse-Marseille' => 4, 'Toulouse-Lille' => 9,
                         'Nice-Marseille' => 2, 'Nice-Lille' => 11, 'Lille-Marseille' => 9
        ];

        for ($i = 0; $i < 100; $i++) {
            $villeDepart = $faker->randomElement($villes);
            $villeArrivee = $faker->randomElement($villes);

            $cleTrajet = $villeDepart . '-' . $villeArrivee;
            $tempsEnHeures = $tempsTrajets[$cleTrajet] ?? $faker->numberBetween(1, 6); // si pas défini

            $dateHeureDepart = $faker->dateTimeBetween('+1 day', '+1 month');
            $dateHeureArrivee = (clone $dateHeureDepart)->modify("+{$tempsEnHeures} hours");

        while ($villeArrivee === $villeDepart) {
               $villeArrivee = $faker->randomElement($villes);
        } 

            $trajet = new Trajet();
            $trajet->setVilleDepart($villeDepart);
            $trajet->setVilleArrivee($villeArrivee);
            $trajet->setDateHeureDepart($dateHeureDepart);
            $trajet->setDateHeureArrivee($dateHeureArrivee);
            $trajet->setPlacesDisponibles($faker->numberBetween(1, 4));
            $trajet->setCredits($faker->numberBetween(20, 100));
            $trajet->setEnergieElectrique($faker->boolean());

            $user = $faker->randomElement($users);
            $trajet->setConducteur($user);
         
            $manager->persist($trajet);
    
        }
    $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}