    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login - DataFuerte</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h1 class="text-xl font-bold mb-4 text-center">Login</h1>
            <form id="login-form" class="space-y-4" action="/" method="POST">
                @csrf
                @method('POST')
                <input type="email" name="email" placeholder="Email" class="w-full border p-2 rounded" required>
                <input type="password" name="password" placeholder="Contraseña" class="w-full border p-2 rounded" required>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Enter</button>
            </form>
            <div id="login-error" class="text-red-600 mt-2 hidden">Authentication error</div>
        </div>



    </body>
    </html>
