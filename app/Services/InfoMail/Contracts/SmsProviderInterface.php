<?php

declare(strict_types=1);

namespace App\Services\InfoMail\Contracts;

interface SmsProviderInterface
{
    /**
     * Send SMS message
     *
     * @return array{success: bool, msg?: string, error?: string}
     */
    public function send(string $phoneNumber, string $message): array;
}
