<?php

declare(strict_types=1);

return [
    '401' => [
        'title' => 'No autorizado',
        'message' => 'Lo sentimos, no está autorizado para acceder a esta página.',
    ],
    '403' => [
        'message' => 'Lo sentimos, tiene prohibido el acceso a esta página.',
    ],
    '404' => [
        'title' => 'Página no encontrada',
        'message' => 'Lo sentimos, la página que busca no pudo ser encontrada.',
    ],
    '419' => [
        'title' => 'Página expirada',
        'message' => 'Lo sentimos, su sesión ha expirado. Por favor, recargue y vuelva a intentarlo.',
    ],
    '429' => [
        'title' => 'Demasiadas solicitudes',
        'message' => 'Lo sentimos, está realizando demasiadas solicitudes a nuestros servidores.',
    ],
    '500' => [
        'title' => 'Error del servidor',
        'message' => 'Vaya, algo salió mal en nuestros servidores.',
    ],
    '503' => [
        'title' => 'Servicio no disponible',
    ],
    'go_home' => 'Ir al Inicio',
];
