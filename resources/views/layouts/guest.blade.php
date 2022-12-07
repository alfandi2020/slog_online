<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" type="image/x-icon" >

    <!-- Styles -->
    <link href="{{ url('css/plugins/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/plugins/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/plugins/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/app.custom.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {{ json_encode(['csrfToken' => csrf_token()]) }}
    </script>
</head>
<body class="login">
    <div class="container" style="position:relative">
    @include('flash::message')
    @yield('content')
    </div>

    <script src="{{ url('js/plugins/jquery.min.js') }}"></script>
    <script src="{{ url('js/plugins/bootstrap.min.js') }}"></script>
</body>
</html>
