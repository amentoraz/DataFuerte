<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DataFuerte</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
        <form id="login-form" class="space-y-5" action="/" method="POST">
            @csrf
            @method('POST')
            <input type="email" name="email" placeholder="Email" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="password" name="password" placeholder="Contraseña" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg font-semibold transition duration-300 ease-in-out">Enter</button>
        </form>
        <div id="login-error" class="text-red-600 mt-4 text-center hidden">Error de autenticación. Por favor, verifica tus credenciales.</div>
    </div>
</body>
</html>