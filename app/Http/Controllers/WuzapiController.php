<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WuzapiService;
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
            'target' => 'required',
            'message' => 'required',
        ]);

        $result = $this->wuzapi->sendText($request->target, $request->message);

        if ($result && isset($result['success']) && $result['success'] === true) {
            return redirect()->back()->with('success', 'Pesan WhatsApp berhasil dikirim (via Wuzapi)!');
        }

        $error = $result['message'] ?? 'Gagal mengirim pesan. Pastikan server Wuzapi menyala.';
        return redirect()->back()->with('error', $error)->withInput();
    }

    /**
     * API: Send text message.
     */
    public function sendTextAPI(Request $request)
    {
        $request->validate([
            'phone'   => 'required',
            'message' => 'required',
        ]);

        $result = $this->wuzapi->sendText($request->phone, $request->message);

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
        try {
            $service = app(WuzapiService::class);
            return $service->sendText($phone, $message);
        } catch (\Exception $e) {
            Log::error("Wuzapi Internal Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
