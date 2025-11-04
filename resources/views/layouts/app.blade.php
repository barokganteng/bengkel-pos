<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>{{ config('app.name', 'Laravel') }}</title>

    @livewireStyles
</head>

<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>

</html>
