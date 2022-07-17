<?php

require_once __DIR__.'/vendor/autoload_runtime.php';

use App\Kernel;


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};