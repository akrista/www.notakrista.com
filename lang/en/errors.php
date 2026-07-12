<?php

declare(strict_types=1);

return [
    '401' => [
        'title' => 'Unauthorized',
        'message' => 'Sorry, you are not authorized to access this page.',
    ],
    '403' => [
        'message' => 'Sorry, you are forbidden from accessing this page.',
    ],
    '404' => [
        'title' => 'Page Not Found',
        'message' => 'Sorry, the page you are looking for could not be found.',
    ],
    '419' => [
        'title' => 'Page Expired',
        'message' => 'Sorry, your session has expired. Please refresh and try again.',
    ],
    '429' => [
        'title' => 'Too Many Requests',
        'message' => 'Sorry, you are making too many requests to our servers.',
    ],
    '500' => [
        'title' => 'Server Error',
        'message' => 'Whoops, something went wrong on our servers.',
    ],
    '503' => [
        'title' => 'Service Unavailable',
    ],
    'go_home' => 'Go Home',
];
