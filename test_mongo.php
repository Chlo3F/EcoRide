<?php

require __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://Jose:Eco2025@localhost:27017/?authSource=admin");

echo "✅ Connexion OK via MongoDB\Client\n";
