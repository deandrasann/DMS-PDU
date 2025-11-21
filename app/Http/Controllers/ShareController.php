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
    public function __invoke(Request $request, $shareToken)
    {
        $userToken = Session::get('token');

        if (!$userToken) {
            return redirect()->route('signin', ['redirect' => "http://127.0.0.1:3000/share/{$shareToken}"]);
        }

        Log::info("User token: " . $userToken);

        $url = "http://127.0.0.1:8000/api/share/{$shareToken}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken,
                'Accept' => 'application/json'
            ])->get($url);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch shared file.', 'details' => $e->getMessage()], 500);
        }
    }
}
