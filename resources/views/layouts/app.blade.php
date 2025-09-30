<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">

</head>
<body class="m-4">
    <div class="d-flex vh-100">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Wrapper Navbar + Content -->
        <div class="d-flex flex-column flex-grow-1">
            <!-- Content -->
            <div class="flex-grow-1 mx-4 mb-2">
                @yield('content')
            </div>
        </div>
    </div>


    <!--Icons-->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
