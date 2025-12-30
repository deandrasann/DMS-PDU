<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }
        public function lastOpen()
    {
        return view('last-opened');
    }
    public function mySpace()
    {
        return view('myspace');
    }
    public function sharedWithMe(Request $request, $path = null)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('signin')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentPath = $path ?? '';

        // Breadcrumb dasar
        $breadcrumb = [
            ['name' => 'Shared with Me', 'url' => route('shared')] // sesuaikan route name
        ];

        $currentFolderName = null;

        // Jika ada path (subfolder), ambil nama folder terakhir untuk JS
        if ($currentPath) {
            $segments = array_filter(explode('/', $currentPath));
            $lastSegmentId = end($segments);

            try {
                $res = Http::withToken($token)
                    ->timeout(10)
                    ->get("https://dms-pdu-api.up.railway.app/api/my-files/{$lastSegmentId}");

                if ($res->successful()) {
                    $data = $res->json();
                    $currentFolderName = $data['name'] ?? $data['folder']['name'] ?? null;
                }
            } catch (\Exception $e) {
                Log::warning("Gagal ambil nama folder shared: {$lastSegmentId}", ['error' => $e->getMessage()]);
            }

            // Build breadcrumb dengan ID sebagai placeholder nama (akan di-update oleh JS)
            $accumulatedPath = '';
            foreach ($segments as $segmentId) {
                $accumulatedPath = $accumulatedPath ? "$accumulatedPath/$segmentId" : $segmentId;
                $breadcrumb[] = [
                    'name' => $segmentId, // placeholder
                    'url'  => route('shared.subfolder', ['path' => $accumulatedPath]),
                    'id'   => $segmentId
                ];
            }
        }

        // Pass token dan data ke view
        return view('shared', compact(
            'token',
            'currentPath',
            'breadcrumb',
            'currentFolderName'
        ));
    }
        public function uploadFile()
    {
        return view('upload-file');
    }
}
