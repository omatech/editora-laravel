<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Editora - {{$title}}</title>
        <link href="{{ asset('/vendor/editora/img/favicon.ico') }}" rel="shortcut icon" />
        @include('editora::components.styles')
    </head>
    <body class="hide-navigation hide-favorites hide-modifications hide-relations @if(isset($body_class)) {{$body_class}} @endif">
        
        @include('editora::components.header')
        @include('editora::templates.main_menu')
        @include('editora::templates.favorites_menu')
        @include('editora::templates.parents_menu')
        @include('editora::templates.last_accessed_menu')
        @yield('body')
        @if(isset($message) && $message!=null) {!!$message!!} @endif
        @include('editora::components.footer')
        @section('modals')
        @show
        @include('editora::components.scripts')
        @section('scripts')
        @show
    </body>
</html>
