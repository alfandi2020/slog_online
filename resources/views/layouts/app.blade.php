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
    {{ Html::style('css/plugins/metisMenu.min.css') }}
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
        @if (auth()->user()->role_id != 7)
        @include('layouts.partials.side-nav')
        @endif

        <div id="page-wrapper" style="padding: 15px;">
            @if (auth()->user()->role_id != 7)
            @include('layouts.partials.top-nav')
            @endif
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
    <script type='text/javascript' src='https://unpkg.com/@zxing/library@latest?ver=5.9.3' id='qrcode-js'></script>
    <script>
    $('div.notifier').not('.alert-important').delay(7000).fadeOut(350);
    </script>
    <script>
            let selectedDeviceId;
            const codeReader = new ZXing.BrowserMultiFormatReader()
            console.log('QR/Barcode Init')

            $(document).ready(function() {
                setTimeout(function() {
                    $('#cameraInit').css('display', 'none');
                    $('#scannerLabel').css('display', 'block');
                }, 1000);
                if ($('#video').length) {
                    codeReader.decodeFromInputVideoDeviceContinuously(undefined, 'video', (result, err) => {
                    if (result) {
                        if ($('#query').length) {
                            $('#query').val(result)
                        }
                        if ($('#manifest_number').length) {
                            $('#manifest_number').val(result)
                        }
                        if ($('#receipt_number').length) {
                            $('#receipt_number').val(result)
                        }
                        if ($('#receipt_number_a').length) {
                            $('#receipt_number_a').val(result)
                        }
                        console.log(`No Resi ${result} berhasil di dapat`)
                        $('#formChecker').submit()
                    }
                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.error(err)
                    }
                })
                console.log(`Camera ID ${selectedDeviceId}`)
                }
            })
    </script>
    @yield('script')
</body>
</html>
