<?php

declare(strict_types=1);

namespace App\Services\Metabase;

use RuntimeException;

final readonly class MetabaseService
{
    private string $metabaseUrl;

    private string $secretKey;

    private int $tokenExpiration;

    public function __construct()
    {
        $this->metabaseUrl = config('notakrista.metabase.url');
        $this->secretKey = config('notakrista.metabase.secret_key');
        $this->tokenExpiration = (int) config('notakrista.metabase.token_expiration', 600);

        throw_if($this->secretKey === '' || $this->secretKey === '0', RuntimeException::class, 'Metabase secret key not configured. Set METABASE_SECRET_KEY in .env');
    }

    /**
     * Generate a signed JWT token for embedding Metabase resources
     *
     * @param  array{question?: int, dashboard?: int}  $resource  The Metabase resource (question or dashboard ID)
     * @param  array<string, mixed>  $params  Query parameters to pass to the embedded resource
     * @return string The signed JWT token
     */
    public function generateToken(array $resource, array $params = []): string
    {
        $payload = [
            'resource' => $resource,
            'params' => (object) $params,
            'exp' => time() + $this->tokenExpiration,
        ];

        return $this->signJwt($payload);
    }

    /**
     * Generate JWT token for a Metabase question
     *
     * @param  int  $questionId  The Metabase question ID
     * @param  array<string, mixed>  $params  Query parameters
     * @return string The JWT token
     */
    public function getQuestionToken(int $questionId, array $params = []): string
    {
        return $this->generateToken(['question' => $questionId], $params);
    }

    /**
     * Generate JWT token for a Metabase dashboard
     *
     * @param  int  $dashboardId  The Metabase dashboard ID
     * @param  array<string, mixed>  $params  Query parameters
     * @return string The JWT token
     */
    public function getDashboardToken(int $dashboardId, array $params = []): string
    {
        return $this->generateToken(['dashboard' => $dashboardId], $params);
    }

    /**
     * Get the Metabase instance URL
     *
     * @return string The Metabase base URL
     */
    public function getInstanceUrl(): string
    {
        return mb_rtrim($this->metabaseUrl, '/');
    }

    /**
     * Sign a JWT token using HS256 algorithm (HMAC-SHA256)
     *
     * @param  array<string, mixed>  $payload  The JWT payload
     * @return string The signed JWT token
     */
    private function signJwt(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $base64UrlEncode = fn (string $data): string => mb_rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

        $headerEncoded = $base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $payloadEncoded = $base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $this->secretKey,
            true
        );

        $signatureEncoded = $base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
}
