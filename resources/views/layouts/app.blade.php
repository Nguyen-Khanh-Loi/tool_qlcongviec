<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#4285f4" />
    <meta name="csrf-token" value="{{ csrf_token() }}"/>
    <title>Quản Lý Dự Án Gosu</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ mix('css/datatable.css') }}" type="text/css" rel="stylesheet"/>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.24/datatables.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <style>
        .bg-light {
            background-color: #eae9e9 !important;
        }
    </style>
</head>
<body>
<div id="app">
</div>
<script>
    window.PUBLIC_PATH = "{{ url('/projectmanager/public/uploads') }}";
</script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script src="{{ mix('js/app.js') }}" type="text/javascript">    
</script>
</body>
</html>