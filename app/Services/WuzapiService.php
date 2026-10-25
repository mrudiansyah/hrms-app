<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use RuntimeException;

class WuzapiService
{
    protected $baseUrl;
    protected $token;
    protected $session;

    public function __construct()
    {
        $this->baseUrl = config('wuzapi.url');
        $this->token = config('wuzapi.token');
        $this->session = config('wuzapi.session');
    }

    /**
     * Send a text message.
     *
     * @param string $to Phone number (e.g., 628123456789)
     * @param string $message
     * @return array|null
     */
    public function sendText($to, $message)
    {
        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])
                ->connectTimeout(5)
                ->timeout(10)
                ->post($this->baseUrl . '/chat/send/text', [
                'Phone'   => $this->formatPhone($to),
                'Body'    => $message,
                'Session' => $this->session,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi Error: ' . $e->getMessage());
            return null;
        }
    }

    public function sendApiText($phone, $message): Response
    {
        $apiKey = config('wuzapi.api_key');
        if (!$apiKey) {
            throw new RuntimeException('Wuzapi send API key is not configured.');
        }

        return Http::acceptJson()
            ->asJson()
            ->withHeaders(['X-API-Key' => $apiKey])
            ->connectTimeout(5)
            ->timeout(10)
            ->post(config('wuzapi.send_api_url'), [
                'phone' => $this->formatPhone($phone),
                'message' => $message,
            ]);
    }

    /**
     * Send an image message.
     *
     * @param string $to Phone number
     * @param string $caption
     * @param string $imageUrl Direct URL to the image
     * @return array|null
     */
    public function sendImage($to, $caption, $imageUrl)
    {
        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->post($this->baseUrl . '/chat/send/image', [
                'Phone'   => $this->formatPhone($to),
                'Caption' => $caption,
                'URL'     => $imageUrl,
                'Session' => $this->session,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi Image Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get connection status.
     *
     * @return array|null
     */
    public function getStatus()
    {
        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/status', [
                'Session' => $this->session,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format phone number to WhatsApp format.
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 08... to 628...
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
