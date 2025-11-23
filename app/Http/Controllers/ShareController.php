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
            return redirect()->route('signin', ['redirect' => "https://dms-pdu-production.up.railway.app/share/{$shareToken}"]);
        }

        Log::info("User token: " . $userToken);

        $url = "https://pdu-dms.my.id/api/share/{$shareToken}";

        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $userToken,
                'Accept' => 'application/json'
            ])->get($url);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch shared file.', 'details' => $e->getMessage()], 500);
        }
    }
}
