<?php

return [
    'paths' => ['api/*'],
    'allowed_origins' => ['https://gestion-ecole-63if.onrender.com'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
    'allowed_origins_patterns' => [],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

