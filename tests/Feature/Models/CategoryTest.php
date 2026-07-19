<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Item;
use App\Models\LanguageLine;

test('uses uuid primary keys', function (): void {
    $category = Category::factory()->create();

    expect($category->id)->toBeString()
        ->and(mb_strlen((string) $category->id))->toBe(36);
});

test('factory writes a single language line under the categories group', function (): void {
    $category = Category::factory()->create();

    $line = LanguageLine::query()
        ->where('group', LanguageLine::CATEGORIES_GROUP)
        ->where('key', $category->slug)
        ->first();

    expect($line)->not->toBeNull()
        ->and($line->text)->toBeArray()
        ->and($line->text['en'] ?? null)->not->toBeNull()
        ->and($line->text['es'] ?? null)->not->toBeNull();
});

test('name falls back to english when locale is missing', function (): void {
    $category = Category::factory()->create([
        'name_en' => 'Tech',
        'name_es' => '',
    ]);

    LanguageLine::query()
        ->where('group', LanguageLine::CATEGORIES_GROUP)
        ->where('key', $category->slug)
        ->update(['text' => ['en' => 'Tech', 'es' => '']]);

    expect($category->name('es'))->toBe('Tech')
        ->and($category->name('de'))->toBe('Tech')
        ->and($category->name('en'))->toBe('Tech');
});

test('route key is the slug', function (): void {
    $category = Category::factory()->create(['slug' => 'my-tools']);

    expect($category->getRouteKeyName())->toBe('slug')
        ->and($category->getRouteKey())->toBe('my-tools');
});

test('has many items', function (): void {
    $category = Category::factory()->create();
    Item::factory()->count(3)->forCategory($category)->create();

    expect($category->items)->toHaveCount(3);
});

test('seven default categories are seeded by the migration', function (): void {
    $slugs = Category::query()->pluck('slug')->all();

    expect($slugs)->toContain('tech', 'clothing', 'book', 'tool', 'kitchenware', 'stationery', 'misc');
});
