<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Dev Processes
    |--------------------------------------------------------------------------
    |
    | Define the processes that run when you execute `php artisan dev`.
    | Each entry has a name (key) and a command spec:
    |
    |   'type'    => 'artisan' | 'node' | 'node-exec'
    |   'command' => the command (without the "php artisan" prefix for artisan),
    |
    | Use 'except' to skip default processes you don't want.
    | Use 'only' to run an exact set of processes (ignores everything else).
    |
    */

    'processes' => [
        'server' => [
            'type' => 'artisan',
            'command' => 'serve --host=localhost',
        ],
        'queue' => [
            'type' => 'artisan',
            'command' => 'queue:listen --tries=1 --timeout=0',
        ],
        'logs' => [
            'type' => 'artisan',
            'command' => 'pail --timeout=0',
        ],
        'vite' => [
            'type' => 'node',
            'command' => 'dev',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Default Processes
    |--------------------------------------------------------------------------
    |
    | List any default process names you want to exclude:
    | e.g., ['queue', 'logs']
    |
    */

    'except' => [],

    /*
    |--------------------------------------------------------------------------
    | Only These Processes
    |--------------------------------------------------------------------------
    |
    | If non-empty, only processes with these names will run.
    | This takes precedence over both 'processes' and 'except'.
    |
    */

    'only' => [],

];
