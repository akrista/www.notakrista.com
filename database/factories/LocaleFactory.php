<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Locale>
 */
final class LocaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = Str::lower(Str::random(4));
        $name = 'Locale ' . mb_strtoupper($code);

        return [
            'code' => $code,
            'name' => $name,
            'native_name' => $name,
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => false,
            'position' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function rtl(): static
    {
        return $this->state(fn (): array => [
            'direction' => 'rtl',
            'code' => 'ar',
            'name' => 'Arabic',
            'native_name' => 'العربية',
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (): array => ['is_default' => true]);
    }
}
