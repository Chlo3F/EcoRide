<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // Forcer APP_ENV en prod si non défini (pour éviter la lecture de .env)
    if (!isset($context['APP_ENV'])) {
        $context['APP_ENV'] = 'prod';
    }
    // APP_DEBUG false par défaut si non défini
    if (!isset($context['APP_DEBUG'])) {
        $context['APP_DEBUG'] = false;
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
