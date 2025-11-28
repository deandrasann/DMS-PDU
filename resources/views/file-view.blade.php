@extends('layouts.app-clean')

@section('content-clean')
@php
    use Illuminate\Support\Str;
    $mime = $file['mime'] ?? ($file['mime_type'] ?? '');
    $url = $file['url'] ?? '';
    $name = $file['name'] ?? 'File Viewer';
@endphp

<div class="container-fluid py-4">
    <h3 class="mb-3">{{ $name }}</h3>

    <div class="card p-4">
        @if (Str::contains($mime, 'pdf'))
            {{-- 📄 PDF --}}
            <iframe src="{{ $url }}" width="100%" height="700" frameborder="0"></iframe>

        @elseif(Str::contains($mime, ['image', 'png', 'jpg', 'jpeg']))
            {{-- 🖼️ Gambar --}}
            <img src="{{ $url }}" alt="Preview" class="img-fluid">

        @elseif(Str::contains($mime, ['sheet', 'excel', 'spreadsheet']))
            {{-- 📊 Excel pakai Luckysheet --}}
            <div id="luckysheet" style="width: 100%; height: 80vh;"></div>


            <script>
                const excelUrl = @json($url);
                const token = @json($token ?? session('token'));
                window.token = @json($token ?? session('token'));


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
                    const workbook = XLSX.read(data, { type: "array" });

                    // Konversi setiap sheet ke format Luckysheet
                    const luckysheetData = workbook.SheetNames.map(name => {
                        const sheet = workbook.Sheets[name];
                        const json = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                        return {
                            name: name,
                            data: json
                        };
                    });

                    // Inisialisasi Luckysheet
                    luckysheet.create({
                        container: 'luckysheet',
                        data: luckysheetData,
                        showinfobar: false, // Hilangkan info bar
                        allowEdit: false,   // Nonaktifkan edit
                        enableAddRow: false,
                        enableAddCol: false,
                        showsheetbar: true, // Tampilkan tab sheet di bawah
                        showtoolbar: false, // Sembunyikan toolbar
                        showstatisticBar: false, // Hilangkan bar bawah
                        enableCopy: false
                    });
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById("luckysheet").innerHTML = `
                        <div class="alert alert-danger mt-3">
                            ❌ Gagal memuat file Excel.<br>
                            <a href="${excelUrl}" target="_blank" class="btn btn-primary mt-2">Download File</a>
                        </div>`;
                });
            </script>

        @elseif(Str::contains($mime, 'word'))
            {{-- 📝 Word pakai Mammoth --}}
            <div id="word-container">Loading Word document...</div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
            <script>
                const url = @json($url);
                fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }                
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.arrayBuffer();
                })
                .then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer }))
                .then(result => {
                    document.getElementById("word-container").innerHTML = result.value;
                })
                .catch(err => {
                    console.error("Word preview failed:", err);
                    document.getElementById("word-container").innerHTML = `
                        <p class="text-danger">Gagal memuat dokumen Word.</p>
                        <a href="${url}" target="_blank" class="btn btn-primary mt-3">Download File</a>
                    `;
                });
            </script>
        @endif
    </div>
</div>
@endsection
