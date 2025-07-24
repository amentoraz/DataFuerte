<form method="POST" action="{{ route('logout') }}" class="flex items-center justify-between">
    @csrf

    <div class="flex items-center space-x-2">
        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
        <span class="text-gray-800 font-semibold text-lg">{{ $user->name }}</span>
    </div>

    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        Logout
    </button>
</form>