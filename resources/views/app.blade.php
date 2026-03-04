<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- Ini buat ambil title dari React Head --}}
    @inertiaHead

    {{-- Script buat konek ke Vite (React) --}}
    @viteReactRefresh 
    @vite(['resources/js/app.jsx'])
</head>
<body class="antialiased font-sans">
    {{-- Di sini tempat React lu bakal muncul --}}
    @inertia
</body>
</html>