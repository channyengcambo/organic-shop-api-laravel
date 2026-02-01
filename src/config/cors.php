<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [env("FRONTEND_URL")],

    'allowed_headers' => [
        'Content-Type',
        'X-App-Key',
        'Authorization',
        'Accept',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
