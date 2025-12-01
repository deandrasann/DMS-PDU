<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        // ← SAMA PERSIS seperti MySpaceController
        if (!$token) {
            return redirect()->route('signin')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentPath = $path ?? '';

        // Breadcrumb
        $breadcrumb = [
            ['name' => 'Shared with Me', 'url' => route('shared')]
        ];

        // Kalau ada path (masuk subfolder), kita pecah ID dan ambil nama dari API
        if ($currentPath) {
            $segments = explode('/', $currentPath);
            $accumulatedPath = '';

            foreach ($segments as $index => $segmentId) {
                if (!$segmentId) continue;

                // Bangun path akumulasi
                $accumulatedPath = $accumulatedPath ? "$accumulatedPath/$segmentId" : $segmentId;

                // Ambil info folder untuk nama (hanya yang terakhir yang perlu nama asli)
                if ($index === count($segments) - 1) {
                    // Ambil nama folder terakhir dari API
                    try {
                        $res = Http::withToken($token)->get("https://pdu-dms.my.id/api/my-files/{$segmentId}");
                        if ($res->successful()) {
                            $data = $res->json();
                            $folderName = $data['name'] ?? $segmentId;
                        } else {
                            $folderName = $segmentId;
                        }
                    } catch (\Exception $e) {
                        $folderName = $segmentId;
                    }
                } else {
                    // Untuk folder sebelumnya, kita tidak tahu namanya → pakai placeholder atau skip
                    // Kita skip dulu karena tidak bisa ambil semua nama tanpa banyak request
                    continue;
                }

                $breadcrumb[] = [
                    'name' => $folderName,
                    'url'  => route('shared.subfolder', ['path' => $accumulatedPath])
                ];
            }
        }

        return view('shared', compact('token', 'currentPath', 'breadcrumb'));
    }
        public function uploadFile()
    {
        return view('upload-file');
    }
}
