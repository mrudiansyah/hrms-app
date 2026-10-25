<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WuzapiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WuzapiController extends Controller
{
    protected $wuzapi;

    public function __construct(WuzapiService $wuzapi)
    {
        $this->middleware('auth')->except(['sendTextAPI', 'statusAPI']);
        $this->wuzapi = $wuzapi;
    }

    /**
     * Display the WhatsApp notification form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('whatsapp.index', [
            'menu' => 'whatsapp_wuzapi'
        ]);
    }

    /**
     * Send message via Web Form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWeb(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string'],
        ]);

        $phone = preg_replace('/\D+/', '', $request->input('phone'));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $apiKey = config('wuzapi.api_key');
        if (!$apiKey) {
            Log::error('Wuzapi send API key is not configured.');

            return redirect()->back()
                ->with('error', 'API key Wuzapi belum dikonfigurasi.')
                ->withInput();
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders(['X-API-Key' => $apiKey])
                ->timeout(20)
                ->post(config('wuzapi.send_api_url'), [
                    'phone' => $phone,
                    'message' => $request->input('message'),
                ]);
        } catch (ConnectionException $exception) {
            Log::error('Wuzapi send API connection failed', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Tidak dapat terhubung ke API Wuzapi.')
                ->withInput();
        }

        $result = $response->json();
        $apiSuccess = is_array($result) ? ($result['success'] ?? true) : true;
        if ($response->successful() && $apiSuccess !== false) {
            return redirect()->back()->with('success', 'Pesan WhatsApp berhasil dikirim via API Wuzapi.');
        }

        $error = is_array($result) && isset($result['message'])
            ? $result['message']
            : 'API Wuzapi gagal mengirim pesan (HTTP ' . $response->status() . ').';

        return redirect()->back()->with('error', $error)->withInput();
    }

    /**
     * API: Send text message.
     */
    public function sendTextAPI(Request $request)
    {
        // if ($err = $this->validateApiKey($request)) return $err;

        $request->validate([
            'target'   => 'required',
            'message' => 'required',
        ]);

        $result = $this->wuzapi->sendText($request->target, $request->message);

        if ($result && isset($result['success']) && $result['success'] === true) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data'    => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send message',
            'error'   => $result,
        ], 500);
    }

    /**
     * API: Check status.
     */
    public function statusAPI()
    {
        // if ($err = $this->validateApiKey($request)) return $err;
        $status = $this->wuzapi->getStatus();

        if ($status) {
            return response()->json([
                'success' => true,
                'status'  => $status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not connect to Wuzapi server',
        ], 500);
    }

    /**
     * Static method to send message from other controllers.
     */
    public static function sendInternalMessage($phone, $message)
    {
        $phone='088148870127';
        try {
            $service = app(WuzapiService::class);
            return $service->sendText($phone, $message);
        } catch (\Exception $e) {
            Log::error("Wuzapi Internal Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    private function validateApiKey(Request $request)
    {
        $key = $request->header('X-API-Key');
        if ($key !== config('wuzapi.api_key')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        return null;
    }
}
