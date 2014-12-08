<?php
return [
    'vulnerabilities' => [
        'sql' => [   
        ],

        'xss' => [     // XSS params
            'stored' => false
        ],

        'csrf' => [    // CSRF params
            'enabled' => false
        ],

        'referrer' => [
            'enabled' => false,
            'hosts' => [$_SERVER['HTTP_HOST']],
            'protocols' => ['http', 'https'],
            'methods' => ['POST'],
            'paths' => ['/']
        ],

        'os_command' => [
            'enabled' => false
        ]
    ]
];


