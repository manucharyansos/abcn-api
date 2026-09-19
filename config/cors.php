<?php

$configuredOrigins = array_filter(array_map('trim', explode(',', env('FRONTEND_URL', ''))));

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_merge([
        'https://abcn.am',
        'https://www.abcn.am',
        'https://preview.abcn.am',
        'http://localhost:5173',
    ], $configuredOrigins))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
