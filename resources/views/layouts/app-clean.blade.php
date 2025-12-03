<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DMS PDU')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/css/pluginsCss.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/plugins.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/css/luckysheet.css" />
        <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/js/plugin.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/luckysheet.umd.js"></script>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="img/favicon.png"type="image/x-icon">
</head>

<body class="d-flex vh-100 overflow-hidden">

    <!-- Konten -->
    <div class="d-flex flex-column flex-grow-1 h-100 overflow-auto w-100 p-4 mt-lg-0 mt-5">
        <div class="content-wrapper">
            @yield('content-clean')
        </div>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    {{-- Di bagian sebelum </body> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="{{ asset('js/myspace.js') }}"></script>
    <script>
        // Set PDF.js worker path
        // pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

        // Pass PHP variables to JavaScript
        window.token = "{{ $token }}";
    </script>

</body>


</html>
