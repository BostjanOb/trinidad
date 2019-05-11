<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Sextant') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 antialiased font-sans h-full">
<div class="flex items-center justify-center h-full">
    <div class="w-1/4">
        <h1 class="text-center mb-4">Sextant</h1>
        <form method="POST" action="{{ route('login') }}" class="border shadow-md bg-white p-8 rounded">
            @csrf

            <h2 class="text-lg mb-4">Login to your account</h2>

            <div class="mb-4">
                <label for="email" class="block font-semibold">E-Mail Address</label>
                <input id="email" type="email" class="block rounded mt-1 py-1 px-2 border w-full" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <div class="text-red-700 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block font-semibold">Password</label>
                <input id="password" type="password" class="block rounded mt-1 mb-4 py-1 px-2 border w-full" name="password" required autocomplete="current-password">
                @error('password')
                <div class="text-red-700 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <label class="flex items-center mb-4">
                <input class="mr-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember Me</span>
            </label>

            <button type="submit" class="bg-blue-500 block font-bold w-full rounded text-white py-2 hover:bg-blue-600">
                Login
            </button>
        </form>
        @if (Route::has('password.request'))
            <div class="text-center mt-2">
                <a class="text-gray-600 text-sm hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            </div>
        @endif
    </div>
</div>
</body>
</html>
