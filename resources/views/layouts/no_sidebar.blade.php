<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    {{ Html::style(url('css/plugins/bootstrap.min.css')) }}
    {{ Html::style('css/plugins/bootstrap-theme.min.css') }}
    @yield('ext_css')
    {{ Html::style(url('css/plugins/sb-admin-2.min.css')) }}
    {{ Html::style(url('css/plugins/font-awesome.min.css')) }}
    {{ Html::style(url('css/app.custom.css')) }}

    <!-- Scripts -->
    <script>
        window.Laravel = {{ json_encode(['csrfToken' => csrf_token()]) }}
    </script>
</head>
<body>
    <div id="wrapper">
        @include('layouts.partials.top-nav-no-sidebar')
        <div class="container-fluid">
            @yield('content')
            @include('flash::message')
        </div>
    </div>

    <!-- Scripts -->
    {{ Html::script(url('js/plugins/jquery.min.js')) }}
    {{ Html::script(url('js/plugins/bootstrap.min.js')) }}
    @stack('ext_js')
    {{ Html::script(url('js/plugins/metisMenu.min.js')) }}
    {{ Html::script(url('js/plugins/sb-admin-2.min.js')) }}
    <script>
    $('div.notifier').not('.alert-important').delay(5000).fadeOut(350);
    </script>
    @yield('script')
</body>
</html>
