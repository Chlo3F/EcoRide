<?php

require_once __DIR__ . '/../../vendor/autoload.php'; // charge les dépendances

$faker = Faker\Factory::create('fr_FR');


$manager = new MongoDB\Driver\Manager("mongodb://Jose:Eco2025@mongodb:27017/?authSource=admin");

// Préparation des données à insérer
$bulk = new MongoDB\Driver\BulkWrite;

for ($i = 0; $i < 20; $i++) {
    $bulk->insert([
        'donneur' => $faker->firstName,
        'conducteur' => $faker->firstName,
        'trajet' => $faker->city . '-' . $faker->city,
        'note' => $faker->numberBetween(1, 5),
        'commentaire' => $faker->sentence(10),
    ]);
}

// Insertion dans la base "Ride", collection "avis"
$manager->executeBulkWrite('Ride.avis', $bulk);

echo "✅ 20 avis ajoutés dans MongoDB.\n";
