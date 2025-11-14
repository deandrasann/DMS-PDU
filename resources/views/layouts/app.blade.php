<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMS PDU')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
</head>

@if (session('token'))
    <script>
        localStorage.setItem('token', '{{ session('token') }}');
    </script>
@endif

<body class="d-flex vh-100 overflow-hidden">

    <!-- Sidebar Desktop -->
    <div class="position-fixed h-100 z-3 d-none d-lg-block">
        @include('partials.sidebar')
    </div>

    <!-- Navbar Mobile -->
    <div class="d-block d-lg-none w-100 position-fixed top-0 start-0 z-3">
        @include('partials.navbar-mobile')
    </div>

    <!-- Konten -->
    <div
        class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto w-100 content-wrapper p-4 mt-lg-0 mt-5">
        {{-- Navbar desktop muncul di atas konten --}}
        <div class="d-none d-lg-block mb-3">
            @include('partials.navbar')
        </div>
        <div class="content-wrapper">
            @yield('content')
        </div>

    </div>



    <!-- JS -->

<body class="d-flex vh-100 overflow-hidden">
    {{-- <div class="position-fixed h-100 z-3">
        @include('partials.sidebar')
    </div> --}}


    {{-- <div class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto p-4">
        @yield('content')
    </div> --}}
    <!--Icons-->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</body>

</html>
