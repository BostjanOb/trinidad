<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sextant') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 antialiased font-sans">
    <div id="app">
        <header class="bg-white">
            <div class="border-b">
                <div class="container flex justify-between items-center py-3">
                    <div class="text-2xl font-semibold">
                        Sextant
                    </div>
                    <div>
                        <div class="flex items-center">
                            <img src="https://placeimg.com/150/150/people" class="rounded-full h-10 w-10">
                            <div class="ml-3 leading-tight">
                                <div class="font-bold">{{ \Auth::user()->name }}</div>
                                <div class="text-gray-600">{{ \Auth::user()->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-b">
                <div class="container flex justify-between py-2">
                    <nav class="flex">
                        <a href="#" class="border-b border-blue-500 text-blue-500 flex items-center px-2">
                            {!! $feather('home', 4, 'mr-1') !!} Dashboard
                        </a>
                        <a href="#" class="flex items-center px-2 mx-4">
                            {!! $feather('sidebar', 4, 'mr-1') !!} Sites
                        </a>
                        <a href="#" class="flex items-center px-2 mr-4">
                            {!! $feather('server', 4, 'mr-1') !!}Servers
                        </a>
                        <a href="#" class="flex items-center px-2">
                            {!! $feather('users', 4, 'mr-1') !!}Users
                        </a>
                    </nav>
                    <div>
                        <form method="POST">
                            <input type="search" placeholder="Search ..." class="border py-1 px-2">
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="py-2 container">
            <div class="flex -mx-2">
                <div class="w-1/2 px-2">
                    <div class="text-center bg-white shadow py-4 leading-tight">
                        <div class="font-semibold text-4xl">19</div>
                        <div class="text-gray-700">sites</div>
                    </div>
                </div>
                <div class="w-1/2 px-2">
                    <div class="text-center bg-white shadow py-4 leading-tight">
                        <div class="font-semibold text-4xl">3</div>
                        <div class="text-gray-700">sites</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
