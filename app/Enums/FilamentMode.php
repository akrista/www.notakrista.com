<?php

declare(strict_types=1);

namespace App\Enums;

enum FilamentMode: string
{
    case Admin = 'admin';
    case App = 'app';

    public static function fromConfig(): self
    {
        $value = config('bizkit.filament_mode');

        return is_string($value) ? (self::tryFrom($value) ?? self::Admin) : self::Admin;
    }

    public function isApp(): bool
    {
        return $this === self::App;
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function panelPath(): string
    {
        return match ($this) {
            self::Admin => 'admin',
            self::App => '',
        };
    }
}
