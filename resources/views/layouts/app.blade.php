<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('public/css/assets/css/bootstrap.css') }}" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{{ asset('public/css/assets/css/custom.css') }}" rel="stylesheet">
    <!-- ChartJS -->
    <link heft="{{ asset('public/js/Chart.js-2.8.0/dist/Chart.css') }}" rel="stylesheet">
    <script src="{{ asset('public/js/Chart.js-2.8.0/dist/Chart.js') }}"></script>
</head>
<body>
    @yield("content")
</body>
</html>
