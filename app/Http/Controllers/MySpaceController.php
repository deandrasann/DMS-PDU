<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MySpaceController extends Controller
{
    public function index($path = '')
    {
        $token = session('token');

        if (!$token) {
            Log::warning('No token provided in session, redirecting to login');
            return redirect()->route('signin')->with('error', 'Please login first');
        }

        $currentPath = $path;

        Log::info('Accessing MySpace', [
            'path' => $path,
            'token_present' => !empty($token),
        ]);

        try {
            // Build URL
            $url = $currentPath ? "https://pdu-dms.my.id/api/my-files/{$currentPath}" : 'https://pdu-dms.my.id/api/my-files';

            Log::info('Calling API', ['url' => $url]);

            // Make API call
            $response = Http::withToken($token)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->get($url);

            Log::info('API Response Status', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('API Data Received', [
                    'files_count' => count($data['files'] ?? []),
                    'has_ancestors' => isset($data['ancestors']),
                    'has_folder' => isset($data['folder']),
                ]);

                // ✅ PERBAIKAN: Handle breadcrumb dengan lebih safe
                $breadcrumb = $this->buildBreadcrumb($data['ancestors'] ?? [], $data['folder'] ?? null, $currentPath);

                return view('myspace', [
                    'currentPath' => $currentPath,
                    'breadcrumb' => $breadcrumb,
                    'files' => $data['files'] ?? [],
                    'token' => $token,
                    'isLastOpenedPage' => false,
                ]);
            }

            // Handle API errors
            if ($response->status() === 401) {
                Log::warning('API returned 401, token invalid');
                session()->forget('token'); // Clear invalid token
                return redirect()->route('signin')->with('error', 'Session expired. Please login again.');
            }

            Log::error('API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            // ✅ PERBAIKAN: Tetap tampilkan MySpace dengan error message
            return view('myspace', [
                'currentPath' => $currentPath,
                'breadcrumb' => $this->buildBreadcrumb([], null, $currentPath), // Empty breadcrumb
                'files' => [],
                'error' => 'Failed to load data from server. Please try again.',
                'token' => $token,
                'isLastOpenedPage' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('MySpace Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('myspace', [
                'currentPath' => $currentPath,
                'breadcrumb' => $this->buildBreadcrumb([], null, $currentPath), // Empty breadcrumb
                'files' => [],
                'error' => 'Connection error: ' . $e->getMessage(),
                'token' => $token,
                'isLastOpenedPage' => false,
            ]);
        }
    }

    public function lastOpened()
    {
        $token = session('token');

        if (!$token) {
            Log::warning('No token provided in session, redirecting to login');
            return redirect()->route('signin')->with('error', 'Please login first');
        }

        Log::info('Accessing Last Opened Page');

        try {
            $url = 'https://pdu-dms.my.id/api/last-opened-files';

            $response = Http::withToken($token)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->get($url);

            Log::info('Last Opened API Response Status', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();

                // ✅ PERBAIKAN: Log structure data untuk debugging
                Log::info('Last Opened API Response Structure', [
                    'has_last_opened_folders' => isset($data['last_opened_folders']),
                    'has_last_opened_files' => isset($data['last_opened_files']),
                    'folders_count' => isset($data['last_opened_folders']) ? count($data['last_opened_folders']) : 0,
                    'files_count' => isset($data['last_opened_files']) ? count($data['last_opened_files']) : 0,
                ]);

                $allFiles = $this->transformLastOpenedData($data);

                return view('last-opened', [
                    'files' => $allFiles,
                    'token' => $token,
                    'isLastOpenedPage' => true,
                ]);
            }

            if ($response->status() === 401) {
                Log::warning('API returned 401, token invalid');
                session()->forget('token');
                return redirect()->route('signin')->with('error', 'Session expired. Please login again.');
            }

            Log::error('Last Opened API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            // ✅ Tetap tampilkan last-opened page dengan data kosong
            return view('last-opened', [
                'files' => [],
                'error' => 'Failed to load last opened items.',
                'token' => $token,
                'isLastOpenedPage' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Last Opened Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('last-opened', [
                'files' => [],
                'error' => 'Connection error: ' . $e->getMessage(),
                'token' => $token,
                'isLastOpenedPage' => true,
            ]);
        }
    }

    /**
     * Transform data dari last-opened endpoint ke format yang kompatibel
     */
    private function transformLastOpenedData($data)
    {
        $transformed = [];

        try {
            // Process folders
            if (isset($data['last_opened_folders']) && is_array($data['last_opened_folders'])) {
                foreach ($data['last_opened_folders'] as $index => $folder) {
                    if (!is_array($folder)) {
                        Log::warning('Invalid folder data at index', ['index' => $index]);
                        continue;
                    }

                    $transformed[] = [
                        'id' => $folder['id'] ?? null,
                        'name' => $folder['name'] ?? 'Unknown Folder',
                        'path' => $folder['path'] ?? '',
                        'storage_path' => $folder['storage_path'] ?? null,
                        'url' => $folder['url'] ?? null,
                        'parent_id' => $folder['parent_id'] ?? null,
                        'is_folder' => true,
                        'mime' => $folder['mime'] ?? null,
                        'size' => $folder['size'] ?? '0.00 B',
                        'type' => $folder['type'] ?? 'Folder',
                        'owner' => $folder['owner'] ?? 'me',
                        'created_at' => $folder['created_at'] ?? null,
                        'updated_at' => $folder['updated_at'] ?? null,
                        'created_by' => $folder['created_by'] ?? null,
                        'updated_by' => $folder['updated_by'] ?? null,
                        'deleted_at' => $folder['deleted_at'] ?? null,
                    ];
                }
            }

            // Process files
            if (isset($data['last_opened_files']) && is_array($data['last_opened_files'])) {
                foreach ($data['last_opened_files'] as $index => $file) {
                    if (!is_array($file)) {
                        Log::warning('Invalid file data at index', ['index' => $index]);
                        continue;
                    }

                    $transformed[] = [
                        'id' => $file['id'] ?? null,
                        'name' => $file['name'] ?? 'Unknown File',
                        'path' => $file['path'] ?? '',
                        'storage_path' => $file['storage_path'] ?? null,
                        'url' => $file['url'] ?? null,
                        'parent_id' => $file['parent_id'] ?? null,
                        'is_folder' => false,
                        'mime' => $file['mime'] ?? null,
                        'size' => $file['size'] ?? '0.00 B',
                        'type' => $file['type'] ?? 'File',
                        'owner' => $file['owner'] ?? 'me',
                        'created_at' => $file['created_at'] ?? null,
                        'updated_at' => $file['updated_at'] ?? null,
                        'created_by' => $file['created_by'] ?? null,
                        'updated_by' => $file['updated_by'] ?? null,
                        'deleted_at' => $file['deleted_at'] ?? null,
                        'labels' => $file['labels'] ?? [],
                    ];
                }
            }

            Log::info('Transformed Last Opened Data', [
                'total_items' => count($transformed),
                'folders_count' => count(array_filter($transformed, fn($item) => $item['is_folder'])),
                'files_count' => count(array_filter($transformed, fn($item) => !$item['is_folder'])),
            ]);
        } catch (\Exception $e) {
            Log::error('Transform Last Opened Data Error', [
                'message' => $e->getMessage(),
                'data_structure' => array_keys($data),
            ]);
        }

        return $transformed;
    }

    public function recommended()
    {
        $token = session('token');

        if (!$token) {
            Log::warning('No token provided in session, redirecting to login');
            return redirect()->route('signin')->with('error', 'Please login first');
        }

        Log::info('Accessing Recommended Files Page');

        try {
            $url = 'https://pdu-dms.my.id/api/recommended-files';

            $response = Http::withToken($token)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->get($url);

            Log::info('Recommended Files API Response Status', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Recommended Files Data', [
                    'files_count' => count($data['recommended_files'] ?? []),
                ]);

                return view('recommended', [
                    'files' => $data['recommended_files'] ?? [],
                    'token' => $token,
                    'isRecommendedPage' => true,
                ]);
            }

            if ($response->status() === 401) {
                Log::warning('API returned 401, token invalid');
                session()->forget('token');
                return redirect()->route('signin')->with('error', 'Session expired. Please login again.');
            }

            Log::error('Recommended Files API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return view('recommended', [
                'files' => [],
                'error' => 'Failed to load recommended files.',
                'token' => $token,
                'isRecommendedPage' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Recommended Files Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('recommended', [
                'files' => [],
                'error' => 'Connection error: ' . $e->getMessage(),
                'token' => $token,
                'isRecommendedPage' => true,
            ]);
        }
    }

private function buildBreadcrumb($ancestors, $currentFolder, $currentPath)
{
    $breadcrumb = [];

    try {
        // ✅ Root item
        $breadcrumb[] = [
            'id' => '',
            'name' => 'MySpace',
            'path' => '',
            'url' => route('myspace'),
        ];

        // Build accumulated path untuk nested folders
        $accumulatedPath = '';

        if (is_array($ancestors)) {
            foreach ($ancestors as $index => $ancestor) {
                if (!is_array($ancestor) || !isset($ancestor['id'], $ancestor['name'])) {
                    continue;
                }

                // ✅ SKIP root folder (email)
                if ($index === 0 && $this->isEmail($ancestor['name'])) {
                    continue;
                }

                // ✅ Build accumulated path untuk nested URL
                if ($accumulatedPath === '') {
                    $accumulatedPath = (string) $ancestor['id'];
                } else {
                    $accumulatedPath .= '/' . (string) $ancestor['id'];
                }

                $breadcrumb[] = [
                    'id' => (string) $ancestor['id'],
                    'name' => $ancestor['name'],
                    'path' => $accumulatedPath,
                    'url' => route('myspace', ['path' => $accumulatedPath]), // ✅ PATH YANG BENAR
                ];
            }
        }

        Log::info('Breadcrumb built', [
            'items_count' => count($breadcrumb),
            'breadcrumb_names' => array_column($breadcrumb, 'name'),
            'current_path' => $currentPath,
        ]);

    } catch (\Exception $e) {
        Log::error('Breadcrumb build error', [
            'message' => $e->getMessage(),
            'current_path' => $currentPath,
        ]);

        $breadcrumb = [
            [
                'id' => '',
                'name' => 'MySpace',
                'path' => '',
                'url' => route('myspace'),
            ],
        ];
    }

    return $breadcrumb;
}

/**
 * Helper method untuk deteksi apakah nama folder adalah email
 */
private function isEmail($name)
{
    // Cek apakah string mengandung @ (email pattern)
    return filter_var($name, FILTER_VALIDATE_EMAIL) !== false ||
           strpos($name, '@') !== false;
}
    // API routes handler
    public function getFiles(Request $request)
    {
        // PERBAIKAN: Ambil token dari session, bukan bearer token
        $token = session('token');
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
        // PERBAIKAN: Ambil token dari session
        $token = session('token');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->timeout(30)
            ->get("https://pdu-dms.my.id/api/view-file/{$fileId}");

            if ($response->successful()) {
                // Return file dengan content type yang sesuai
                $contentType = $response->header('Content-Type', 'application/octet-stream');

                return response($response->body(), 200)->header('Content-Type', $contentType)->header('Access-Control-Allow-Origin', '*');
            }

            return response()->json(['error' => 'File not found'], 404);
        } catch (\Exception $e) {
            Log::error('Proxy file error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }

 public function viewFile($fileId)
{
    $token = Session::get('token');

    Log::info('Viewing file', [
        'file_id' => $fileId,
        'token_present' => $token,
    ]);

        if (!$token) {
            Log::warning('No token in session for file view', ['file_id' => $fileId]);
            return redirect()->route('signin')->with('error', 'Please login first');
        }

    try {

        Log::info('User Token', [
            'token_present' => $token,
        ]);

        $listResponse = Http::connectTimeout(5)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->get("https://pdu-dms.my.id/api/file-info/{$fileId}");

            if (!$listResponse->successful()) {
                Log::error('Failed to fetch files for file view', [
                    'status' => $listResponse->status(),
                    'file_id' => $fileId,
                ]);
                abort(404, 'Cannot fetch files');
            }

        $data = $listResponse->json();

        Log::info('File view data structure', [
            'data_keys' => array_keys($data),
        ]);

        $files = $data['file_info'];

        Log::info('Data fetched:', [
            'file info' => $files,
        ]);

        return view('file-view', [
            'fileId' => $fileId,
            'file' => $files,
            'token' => $token,
        ]);

    } catch (\Exception $e) {
        Log::error('File view error', [
            'file_id' => $fileId,
            'error' => $e->getMessage(),
        ]);
        abort(500, 'Failed to load file');
    }
}


    public function upload(Request $request)
    {
        // PERBAIKAN: Ambil token dari session
        $token = session('token');
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Validasi ada file
            if (!$request->hasFile('files')) {
                return response()->json(['error' => 'No files uploaded'], 400);
            }

            // Persiapkan multipart data
            $multipartData = [];

            // Tambahkan setiap file
            foreach ($request->file('files') as $file) {
                $multipartData[] = [
                    'name' => 'files[]',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];
            }

            // Kirim ke API
            $response = Http::withToken($token)->asMultipart()->post('https://pdu-dms.my.id/api/upload', $multipartData);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(
                [
                    'error' => 'Upload failed',
                    'status' => $response->status(),
                ],
                $response->status(),
            );
        } catch (\Exception $e) {
            Log::error('Upload Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Upload failed'], 500);
        }
    }

    // Method untuk membuka folder
    public function openFolder(Request $request, $folderId)
    {
        // PERBAIKAN: Ambil token dari session
        $token = session('token');

        if (!$token) {
            return response()->json(
                [
                    'error' => 'Unauthenticated',
                    'message' => 'Please login first',
                ],
                401,
            );
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])
                ->timeout(30)
                ->get("https://pdu-dms.my.id/api/folders/{$folderId}");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(
                    [
                        'error' => 'Failed to fetch folder',
                        'status' => $response->status(),
                    ],
                    $response->status(),
                );
            }
        } catch (\Exception $e) {
            Log::error('Open Folder Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to open folder'], 500);
        }
    }

    // Method untuk proxy API (untuk menghindari CORS)
    public function proxyApi(Request $request, $endpoint)
    {
        // PERBAIKAN: Ambil token dari session
        $token = session('token');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $url = "https://pdu-dms.my.id/api/{$endpoint}";

            // Forward method dan headers
            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false,
                ])
                ->send($request->method(), $url, [
                    'query' => $request->query(),
                    'json' => $request->json()->all(),
                ]);

            return response($response->body(), $response->status())->withHeaders($response->headers());
        } catch (\Exception $e) {
            Log::error('Proxy API Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }

    // Method untuk menampilkan form edit file
    public function editFile($fileId)
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get file details
            $fileResponse = Http::withToken($token)
                ->withOptions(['verify' => false])
                ->get('https://pdu-dms.my.id/api/my-files');

            if (!$fileResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch file'], 500);
            }

            $data = $fileResponse->json();
            $files = $data['files'] ?? [];
            $file = collect($files)->firstWhere('id', (int) $fileId);

            if (!$file) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Get all labels
            $labelsResponse = Http::withToken($token)
                ->withOptions(['verify' => false])
                ->get('https://pdu-dms.my.id/api/labels');

            $labels = $labelsResponse->successful() ? $labelsResponse->json()['data'] ?? [] : [];

            return response()->json([
                'file' => $file,
                'labels' => $labels,
            ]);
        } catch (\Exception $e) {
            Log::error('Edit file error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }

    // Method untuk update file - DIPERBAIKI untuk FormData
    public function updateFile(Request $request, $fileId)
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Handle labels dari FormData
            $labels = [];
            if ($request->has('labels')) {
                $labels = is_array($request->labels) ? $request->labels : json_decode($request->labels, true) ?? [];
            }

            $updateData = [
                'title' => $request->title,
                'labels' => $labels,
            ];

            Log::info('Updating file:', ['file_id' => $fileId, 'data' => $updateData]);

            $response = Http::withToken($token)
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->put("https://pdu-dms.my.id/api/update-file/{$fileId}", $updateData);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'File updated successfully',
                ]);
            }

            return response()->json(
                [
                    'error' => 'Update failed: ' . ($response->body() ?? 'Unknown error'),
                ],
                $response->status(),
            );
        } catch (\Exception $e) {
            Log::error('Update file exception:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Service unavailable'], 500);
        }
    }
}
