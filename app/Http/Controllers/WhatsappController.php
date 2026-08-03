<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    /**
     * Send message via Fonnte API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'required',
            'url' => 'nullable|url',
        ]);

        $token = env('FONNTE_TOKEN');
        
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Fonnte token not found in environment configuration.',
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $request->target,
                'message' => $request->message,
                'url' => $request->url, // Optional: for sending media
                'delay' => '2', // Optional: recommended delay for multiple targets
                'countryCode' => '62', // Optional: default to Indonesia
            ]);

            $result = $response->json();

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Message sent successfully',
                    'data' => $result,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Failed to send message via Fonnte',
                'error' => $result,
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Whatsapp Notification Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while sending the message.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
