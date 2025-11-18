<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MySpaceController extends Controller
{
    public function index($path = '')
    {
        // CEK TOKEN dari header atau query parameter
        $token = request()->bearerToken();
        if (!$token) {
            $token = request()->query('token');
        }

        // Jika tidak ada token, redirect ke login
        if (!$token) {
            Log::warning('No token provided, redirecting to login');
            return redirect()->route('signin')->with('error', 'Please login first');
        }

        $currentPath = $path;

        Log::info('Accessing MySpace', [
            'path' => $path,
            'token_present' => !empty($token),
            'session_id' => session()->getId()
        ]);

        try {
            // Build URL
            $url = $currentPath
                ? "https://pdu-dms.my.id/api/my-files/{$currentPath}"
                : "https://pdu-dms.my.id/api/my-files";

            Log::info('Calling API', ['url' => $url]);

            // Make API call dengan token
            $response = Http::withToken($token)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])->get($url);

            Log::info('API Response Status', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('API Data Received', ['files_count' => count($data['files'] ?? [])]);

                // Build breadcrumb
                $breadcrumb = $this->buildBreadcrumb($data['ancestors'] ?? [], $data['folder'] ?? null, $currentPath);

                return view('myspace', [
                    'currentPath' => $currentPath,
                    'breadcrumb' => $breadcrumb,
                    'files' => $data['files'] ?? [],
                    'token' => $token // Kirim token ke view
                ]);
            }

            // Handle API errors
            if ($response->status() === 401) {
                Log::warning('API returned 401, token invalid');
                return redirect()->route('signin')->with('error', 'Session expired. Please login again.');
            }

            Log::error('API Error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return view('myspace', [
                'currentPath' => $currentPath,
                'breadcrumb' => [],
                'error' => 'Failed to load data from server. Please try again.',
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('MySpace Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return view('myspace', [
                'currentPath' => $currentPath,
                'breadcrumb' => [],
                'error' => 'Connection error: ' . $e->getMessage(),
                'token' => $token
            ]);
        }
    }

    private function buildBreadcrumb($ancestors, $currentFolder, $currentPath)
    {
        $breadcrumb = [];

        // Always start with root
        $breadcrumb[] = [
            'id' => '',
            'name' => 'MySpace',
            'path' => ''
        ];

        // Add ancestors
        foreach ($ancestors as $ancestor) {
            if (isset($ancestor['id']) && isset($ancestor['name'])) {
                $breadcrumb[] = [
                    'id' => $ancestor['id'],
                    'name' => $ancestor['name'],
                    'path' => $this->buildPath($breadcrumb, $ancestor['id'])
                ];
            }
        }

        // Add current folder if exists
        if ($currentFolder && isset($currentFolder['id']) && isset($currentFolder['name'])) {
            $lastItem = end($breadcrumb);
            if (!$lastItem || $lastItem['id'] !== $currentFolder['id']) {
                $breadcrumb[] = [
                    'id' => $currentFolder['id'],
                    'name' => $currentFolder['name'],
                    'path' => $currentPath
                ];
            }
        }

        return $breadcrumb;
    }

    private function buildPath($breadcrumb, $newId)
    {
        $path = '';
        foreach ($breadcrumb as $item) {
            if ($item['id'] && $item['id'] !== '') {
                $path .= $item['id'] . '/';
            }
        }
        return rtrim($path . $newId, '/');
    }

    // API routes handler
    public function getFiles(Request $request)
    {
        // Cek token dari request
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $response = Http::withToken($token)->get('https://pdu-dms.my.id/api/my-files');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'API request failed'], $response->status());

        } catch (\Exception $e) {
            Log::error('getFiles Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }

    public function proxyPdf($fileId)
    {
        // Cek token dari request
        $token = request()->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $response = Http::withToken($token)->get("https://pdu-dms.my.id/api/view-file/{$fileId}");

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', 'application/pdf');
            }

            return response()->json(['error' => 'File not found'], 404);

        } catch (\Exception $e) {
            Log::error('proxyPdf Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }

    public function viewFile($fileId)
    {
        // Cek token dari request
        $token = request()->bearerToken();
        if (!$token) {
            return redirect()->route('signin');
        }

        try {
            $response = Http::withToken($token)->get('https://pdu-dms.my.id/api/my-files');

            if (!$response->successful()) {
                abort(404, 'Cannot fetch files');
            }

            $data = $response->json();
            $files = $data['files'] ?? [];
            $fileData = collect($files)->firstWhere('id', (int) $fileId);

            if (!$fileData) {
                abort(404, 'File not found');
            }

            return view('file-view', [
                'fileId' => $fileId,
                'file' => $fileData,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            abort(500, 'Failed to load file');
        }
    }

    public function upload(Request $request)
    {
        // Cek token dari request
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $http = Http::withToken($token)->asMultipart();

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $http = $http->attach('files[]', file_get_contents($file->getRealPath()), $file->getClientOriginalName());
                }
            }

            $response = $http->post('https://pdu-dms.my.id/api/upload-files', [
                'parent_id' => $request->input('parent_id'),
                'relative_paths' => $request->input('relative_paths', [])
            ]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Upload Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Upload failed'], 500);
        }
    }
}
