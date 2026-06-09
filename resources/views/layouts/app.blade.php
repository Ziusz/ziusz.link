<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? null])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        {{ $slot }}

        @livewireScripts
    </body>
</html>
