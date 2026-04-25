<?php

return [
    'agent' => [
        'key' => env('JWT_AGENT_KEY'),
        'ttl' => env('JWT_AGENT_TTL', 28800),
        'iss' => env('JWT_AGENT_ISS', null),
    ],
    'default' => [
        'key' => env('JWT_DEFAULT_KEY'),
        'ttl' => env('JWT_DEFAULT_TLL'),
    ],
    'algo' => 'HS256',

];