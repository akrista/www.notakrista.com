<?php

declare(strict_types=1);

return [
    'personal_information' => 'Personal Information',
    'personal_data_desc' => 'Manage user profile information and credentials.',
    'audit_information' => 'Audit Information',
    'audit_information_desc' => 'System activity logs for this user account.',

    'display' => 'Display',
    'translations' => 'Translations',
    'stats' => 'Stats',
    'inventory' => 'Inventory',
    'equipment' => 'Equipment',
    'equipment_desc' => 'Assign this item to a loadout slot to feature it on the character page. Leave both fields blank to keep it as an inventory-only item.',

    'translation_identity' => 'Identity',
    'translation_identity_desc' => 'The (group, key) pair uniquely identifies a translatable string. These must match the keys used in the source code.',
    'translation_values' => 'Localized values',
    'translation_values_desc' => 'One entry per locale. Values stored here override the matching entry in lang/<locale>/<group>.php.',

    'sync_translations_from_files' => 'Sync translations from lang files',
    'sync_translations_from_files_desc' => 'Scan every lang/<locale>/*.php file and seed any missing (group, key, locale) values into the database. Existing values in the database are preserved — this is a one-way merge, not an overwrite.',
];
