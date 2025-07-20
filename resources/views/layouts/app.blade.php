<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Aplicación Laravel')</title> {{-- Título dinámico con un valor por defecto --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    @yield('head_extra') 
</head>
<body class="bg-gray-100 p-8">

    <div class="container mx-auto">
        @yield('content')
    </div>


    @yield('scripts_extra')
</body>
</html>