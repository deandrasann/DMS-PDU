@extends('layouts.app-clean')

@section('content-clean')
    @php
        use Illuminate\Support\Str;
        $mime = $file['mime'] ?? ($file['mime_type'] ?? '');
        $url = $file['url'] ?? '';
        $name = $file['name'] ?? 'File Viewer';
    @endphp

    <div class="container py-4">
        <h3 class="mb-3">{{ $name }}</h3>

        <div class="card p-4">

            @if (Str::contains($mime, 'pdf'))
                {{-- 📄 PDF --}}
                <iframe src="{{ $url }}" width="100%" height="700" frameborder="0"></iframe>
            @elseif(Str::contains($mime, ['image', 'png', 'jpg', 'jpeg']))
                {{-- 🖼️ Gambar --}}
                <img src="{{ $url }}" alt="Preview" class="img-fluid">
            @elseif(Str::contains($mime, ['sheet', 'excel', 'spreadsheet']))
                {{-- 📊 Excel pakai SheetJS --}}
                <div id="excel-preview" class="border p-3 bg-light rounded">Memuat file Excel...</div>

                {{-- Script SheetJS --}}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
                <script>
                    const excelUrl = @json($url);
                    const token = @json($token ?? session('token'));

                    fetch(excelUrl, {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/octet-stream'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error(`Gagal mengambil file Excel. Status: ${res.status}`);
                            return res.arrayBuffer();
                        })
                        .then(data => {
                            const workbook = XLSX.read(data, {
                                type: "array"
                            });
                            const firstSheet = workbook.SheetNames[0];
                            const html = XLSX.utils.sheet_to_html(workbook.Sheets[firstSheet]);
                            document.getElementById("excel-preview").innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById("excel-preview").innerHTML = `
                <p class="text-danger">❌ Gagal memuat file Excel.</p>
                <a href="${excelUrl}" target="_blank" class="btn btn-primary mt-3">Download File</a>
            `;
                        });
                </script>
            @elseif(Str::contains($mime, 'word'))
                <div id="word-container">Loading Word document...</div>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
                <script>
                    const url = @json($url);

                    fetch(url, {
                            headers: {
                                // kalau API butuh token, tambahkan di sini
                                'Authorization': 'Bearer {{ $token ?? '' }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.arrayBuffer();
                        })
                        .then(arrayBuffer => {
                            return window.mammoth.convertToHtml({
                                arrayBuffer: arrayBuffer
                            });
                        })
                        .then(result => {
                            document.getElementById("word-container").innerHTML = result.value;
                        })
                        .catch(err => {
                            console.error("Word preview failed:", err);
                            document.getElementById("word-container").innerHTML = `
                <p class="text-danger">❌ Gagal memuat dokumen Word.</p>
                <a href="${url}" target="_blank" class="btn btn-primary mt-3">Download File</a>
            `;
                        });
                </script>
            @endif

        </div>
    </div>
@endsection
