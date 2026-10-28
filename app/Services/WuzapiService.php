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
        $this->baseUrl = config('wuzapi.url', 'http://192.168.1.4:8000');
        $this->token = config('wuzapi.token', 'rahasia123');
        $this->session = config('wuzapi.session', 'default');
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
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'token' => $this->token ?: 'rahasia123',
                ])
                ->connectTimeout(5)
                ->timeout(10)
                ->post($this->baseUrl . '/chat/send/text', [
                    'Phone'   => $this->formatPhone($to),
                    'Body'    => $message,
                    'Session' => $this->session ?: 'default',
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi Error: ' . $e->getMessage());
            return null;
        }
    }

    public function sendApiText($phone, $message): Response
    {
        $apiKey = config('wuzapi.api_key') ?: 'SummitAdyawinsa2014';
        $formattedPhone = $this->formatPhone($phone);
        $sendUrl = config('wuzapi.send_api_url', 'https://ems.summitadyawinsa.co.id/EMS/api/wuzapi/send');

        $caBundle = config('wuzapi.ca_bundle');
        $verifyOption = (is_string($caBundle) && $caBundle !== '' && is_file($caBundle))
            ? $caBundle
            : false;

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders(['X-API-Key' => $apiKey])
                ->withOptions([
                    'verify' => $verifyOption,
                ])
                ->connectTimeout(5)
                ->timeout(12)
                ->post($sendUrl, [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return $response;
            }
        } catch (\Throwable $e) {
            Log::warning('Primary Wuzapi send_api_url failed, attempting LAN IP fallback: ' . $e->getMessage());
        }

        try {
            $fallbackResponse = Http::withoutVerifying()
                ->acceptJson()
                ->asJson()
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Host' => 'ems.summitadyawinsa.co.id',
                ])
                ->connectTimeout(5)
                ->timeout(12)
                ->post('https://192.168.1.4/EMS/api/wuzapi/send', [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ]);

            if ($fallbackResponse->successful()) {
                return $fallbackResponse;
            }
        } catch (\Throwable $e) {
            Log::warning('LAN IP EMS Wuzapi endpoint failed, attempting direct Wuzapi port 8000 fallback: ' . $e->getMessage());
        }

        $directUrl = rtrim($this->baseUrl ?: 'http://192.168.1.4:8000', '/') . '/chat/send/text';
        return Http::withoutVerifying()
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'token' => $this->token ?: 'rahasia123',
            ])
            ->connectTimeout(5)
            ->timeout(12)
            ->post($directUrl, [
                'Phone' => $formattedPhone,
                'Body' => $message,
                'Session' => $this->session ?: 'default',
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
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'token' => $this->token ?: 'rahasia123',
                ])->post($this->baseUrl . '/chat/send/image', [
                    'Phone'   => $this->formatPhone($to),
                    'Caption' => $caption,
                    'URL'     => $imageUrl,
                    'Session' => $this->session ?: 'default',
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
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'token' => $this->token ?: 'rahasia123',
                ])->get($this->baseUrl . '/session/status', [
                    'Session' => $this->session ?: 'default',
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
