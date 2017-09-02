<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '多彩市场') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
      window.Laravel = {
        csrfToken: "{{ csrf_token() }}"
      };

      window.User = {!! Auth::user() !!};
    </script>
</head>
<body>
<div id="app">
</div>

<!-- 富文本编辑器 Scripts -->
<script src="./static/tinymce/tinymce.min.js"></script>
<!-- Admin Scripts -->
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
