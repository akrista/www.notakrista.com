<?php

declare(strict_types=1);

namespace App\Services\InfoMail;

use App\Services\InfoMail\Contracts\SmsProviderInterface;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final readonly class SmsService implements SmsProviderInterface
{
    private string $apiUrl;

    private string $apiUser;

    private string $apiPassword;

    public function __construct()
    {
        $this->apiUrl = config('notakrista.sms.api_url');
        $this->apiPassword = config('notakrista.sms.api_password');
        $this->apiUser = config('notakrista.sms.api_user');
    }

    /**
     * Send SMS message
     *
     * @return array{success: bool, msg?: string, error?: string}
     */
    public function send(string $phoneNumber, string $message): array
    {
        try {
            $data = [
                'username' => $this->apiUser,
                'password' => $this->apiPassword,
                'number' => $phoneNumber,
                'message' => $message,
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->apiUrl . '/back-end/api/messages/send', $data);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'msg' => $data['msg'] ?? $data['message'] ?? 'SMS sent successfully',
                ];
            }

            $error = $response->json('data.message') ??
                   $response->json('error') ??
                   $response->json('message') ??
                   'Unknown error';

            return [
                'success' => false,
                'error' => $error,
            ];
        } catch (RequestException $e) {
            $error = 'Service temporarily unavailable';

            if ($e->response) {
                $error .= ': ' . $e->response->status() . ' ' . $e->response->reason();
            }

            return [
                'success' => false,
                'error' => $error,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to send SMS: ' . $e->getMessage(),
            ];
        }
    }
}
