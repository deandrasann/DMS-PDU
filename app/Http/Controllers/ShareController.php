<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ShareController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $token)
    {
        $userToken = Session::get('token');

        Log::info("User token from session: " . ($userToken ?? 'null'));

        if (!$userToken) {
            return redirect()->route('signin', [
                'redirect' => "https://dms-pdu-production.up.railway.app/share/$token"
            ]);
        }

        Log::info("Fetching share link with token: $token for user token: $userToken");

        try {
            $response = Http::connectTimeout(5)
                ->withHeaders([
                'Authorization' => 'Bearer ' . $userToken,
                    'Accept' => 'application/json',
                ])->get("https://pdu-dms.my.id/api/share/$token");

            // $response = Http::connectTimeout(5)
            //     ->withHeaders([
            //     'Authorization' => 'Bearer ' . $userToken,
            //         'Accept' => 'application/json',
            //     ])->get("http://127.0.0.1:8000/api/share/$token");

            $response->throw(); // ini akan memicu exception kalau gagal
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Log ke laravel.log
            Log::error("Share API Error", [
                'error' => $e->getMessage(),
                'body' => $e->response?->body(),
            ]);

            // Tampilkan error response ke browser
            return response($e->response?->body() ?? $e->getMessage(), 500);
        }
    }
}
