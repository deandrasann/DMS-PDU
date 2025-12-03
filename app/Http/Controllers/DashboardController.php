<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        // Breadcrumb awal
        $breadcrumb = [
            ['name' => 'Shared with Me', 'url' => route('shared')]
        ];

        $currentFolderName = null; // Untuk dikirim ke JS

        // Kalau ada path, ambil HANYA nama folder terakhir (current)
        if ($currentPath) {
            $segments = array_filter(explode('/', $currentPath));
            $lastSegmentId = end($segments);

            try {
                $res = Http::withToken($token)
                    ->timeout(3)
                    ->get("https://pdu-dms.my.id/api/my-files/{$lastSegmentId}");

                if ($res->successful()) {
                    $data = $res->json();
                    $currentFolderName = $data['name'] ?? $data['folder']['name'] ?? null;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch folder name for ID: {$lastSegmentId}");
            }

            // Breadcrumb sementara pakai ID (akan diupdate JS)
            $accumulatedPath = '';
            foreach ($segments as $segmentId) {
                $accumulatedPath = $accumulatedPath ? "$accumulatedPath/$segmentId" : $segmentId;

                $breadcrumb[] = [
                    'name' => $segmentId, // Placeholder
                    'url'  => route('shared.subfolder', ['path' => $accumulatedPath]),
                    'id'   => $segmentId  // Tambahkan ID untuk JS
                ];
            }
        }

        return view('shared', compact('token', 'currentPath', 'breadcrumb', 'currentFolderName'));
    }
        public function uploadFile()
    {
        return view('upload-file');
    }
}
