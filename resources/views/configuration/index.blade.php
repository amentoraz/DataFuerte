@extends('layouts.app')

@section('title', 'Configuration')

@section('content')

<div class="container mx-auto px-6">

    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        @include('partials.header')
    </div>

    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 

        <h1 class="text-3xl font-bold mb-6 text-gray-800 pt-6">Configuration</h1>

        <form action="{{ route('configuration.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="mb-4">
                <label for="iterations" class="block mb-2 text-sm text-gray-600">PBKDF2 Iterations</label>
                <input type="number" name="iterations" id="iterations"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter number of iterations"
                       value="1000000"
                       min="200000"
                       >
            </div>

            <div>
                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"> {{-- Added focus styles --}}
                    Save
                </button>
            </div>
        </form>
    </div>

</div>

@endsection