<?php

declare(strict_types=1);

return [
    'personal_information' => 'Información Personal',
    'personal_data_desc' => 'Gestione la información del perfil del usuario y las credenciales.',
    'audit_information' => 'Información de Auditoría',
    'audit_information_desc' => 'Registros de actividad del sistema para esta cuenta de usuario.',

    'display' => 'Mostrar',
    'translations' => 'Traducciones',
    'stats' => 'Estadísticas',
    'inventory' => 'Inventario',
    'equipment' => 'Equipamiento',
    'equipment_desc' => 'Asigne este objeto a una ranura de equipamiento para destacarlo en la página de personaje. Deje ambos campos vacíos para mantenerlo solo en el inventario.',

    'translation_identity' => 'Identidad',
    'translation_identity_desc' => 'El par (grupo, clave) identifica de manera única una cadena traducible. Deben coincidir con las claves utilizadas en el código fuente.',
    'translation_values' => 'Valores traducidos',
    'translation_values_desc' => 'Una entrada por cada locale. Los valores guardados aquí anulan la entrada correspondiente en lang/<locale>/<group>.php.',

    'sync_translations_from_files' => 'Sincronizar traducciones desde archivos de idioma',
    'sync_translations_from_files_desc' => 'Escanee cada archivo lang/<locale>/*.php y siembre cualquier valor faltante (grupo, clave, locale) en la base de datos. Se conservan los valores existentes en la base de datos — esta es una fusión unidireccional, no una sobreescritura.',
];
