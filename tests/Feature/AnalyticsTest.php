<?php

declare(strict_types=1);

test('analytics scripts are not rendered when configuration is empty', function (): void {
    config([
        'services.google.analytics_id' => null,
        'services.clarity.project_id' => null,
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertDontSee('https://www.googletagmanager.com/gtag/js')
        ->assertDontSee('https://www.clarity.ms/tag/');
});

test('google analytics script is rendered when configured', function (): void {
    config([
        'services.google.analytics_id' => 'G-TEST123456',
        'services.clarity.project_id' => null,
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123456', false)
        ->assertSee("gtag('config', 'G-TEST123456');", false)
        ->assertDontSee('https://www.clarity.ms/tag/');
});

test('microsoft clarity script is rendered when configured', function (): void {
    config([
        'services.google.analytics_id' => null,
        'services.clarity.project_id' => 'clarity12345',
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertDontSee('https://www.googletagmanager.com/gtag/js')
        ->assertSee('https://www.clarity.ms/tag/"', false)
        ->assertSee('clarity12345', false);
});

test('privacy disclaimer banner and footer link are rendered on public pages', function (): void {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Privacy & Analytics', false)
        ->assertSee('[Privacy]');
});
