<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MySpaceController extends Controller
{
    public function index()
    {
        $token = session('token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://pdu-dms.my.id/api/files');

        $files = $response->json()['files'] ?? [];

        // dd($files);
        return view('myspace', compact('files'));
    }


    public function getFiles(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please login first'
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(30)->get('http://pdu-dms.my.id/api/my-files/');

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Failed to fetch files',
                    'status' => $response->status()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function proxyPdf(Request $request, $fileId)
    {
        $token = session('token') ?? $request->bearerToken();

        if (!$token) {
            dd(session('token'));

            return response()->json(['error' => 'Unauthorized: No token found'], 401);

        }


        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->timeout(30)
            ->get("http://pdu-dms.my.id/api/view-file/{$fileId}");

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            return response()->json([
                'error' => 'Failed to fetch PDF',
                'status' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

public function viewFile($fileId)
{
    $token = session('token') ?? request()->bearerToken();

    if (!$token) {
        abort(401, 'Unauthorized');
    }

    try {
        $listResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->timeout(30)->get("http://pdu-dms.my.id/api/my-files");

        if (!$listResponse->successful()) {
            abort(404, 'Cannot fetch files list');
        }

        $json = $listResponse->json();

        // ✅ Ambil hanya bagian 'files' (isi daftar file sebenarnya)
        $files = $json['files'] ?? [];

        // 🔍 Cari file berdasarkan ID
        $fileData = collect($files)->firstWhere('id', (int) $fileId);

        if (!$fileData) {
            abort(404, 'File not found');
        }

        // ✅ Kirim ke view
        return view('file-view', [
            'fileId' => $fileId,
            'file' => $fileData, // ubah ke 'file' biar sama dengan Blade kamu
            'token' => $token,
        ]);

    } catch (\Exception $e) {
        abort(500, 'Failed to load file: ' . $e->getMessage());
    }
}





    // Method untuk membaca file PDF
    public function readFile(Request $request, $fileId)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please login first'
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(30)->get("http://pdu-dms.my.id/api/files/{$fileId}");

            if ($response->successful()) {
                $fileData = $response->json();

                // Cek jika file adalah PDF
                if (isset($fileData['mime_type']) && $fileData['mime_type'] === 'application/pdf') {
                    return response()->json([
                        'success' => true,
                        'file' => $fileData,
                        'view_url' => route('pdf.view', ['fileId' => $fileId])
                    ]);
                } else {
                    return response()->json([
                        'error' => 'File is not a PDF',
                        'message' => 'Only PDF files can be viewed'
                    ], 400);
                }
            } else {
                return response()->json([
                    'error' => 'Failed to fetch file',
                    'status' => $response->status()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk membuka folder
    public function openFolder(Request $request, $folderId)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please login first'
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(30)->get("http://pdu-dms.my.id/api/folders/{$folderId}");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Failed to fetch folder',
                    'status' => $response->status()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
