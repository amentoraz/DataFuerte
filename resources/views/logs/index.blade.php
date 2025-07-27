@extends('layouts.app')

@section('title', 'Configuration')

@section('content')

<div class="container mx-auto md:px-6">

    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        @include('partials.header')
    </div>

    @include('partials.pestanyas')


    <div class="bg-white shadow-md rounded-lg p-4 pb-0 mb-6">

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none';">
                        <title>Cerrar</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697L8.303 10l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 **text-red-500**" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none';">
                        <title>Cerrar</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697L8.303 10l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif



        @if ($logs->isEmpty())
            <p class="text-red-600 pb-4">There are no logs registered yet.</p>
        @else
            {{-- Big screens (hidden in small ones) --}}
            <div class="hidden sm:block overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created at
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loggable Type
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loggable ID
                            </th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($logs as $log)
                            <tr>
                                <td class="px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2">{{ $log->user_id }}</td>
                                <td class="px-4 py-2">{{ $log->action }}</td>
                                <td class="px-4 py-2">{{ $log->loggable_type }}</td>
                                <td class="px-4 py-2">{{ $log->loggable_id }}</td>
                                <td class="px-4 py-2">{{ $log->data }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Small screens (hidden in big ones) --}}

        <div class="sm:hidden grid grid-cols-1 gap-3">
            @foreach ($logs as $log)
                {{-- Usamos un div principal para la tarjeta que actuará como el elemento clicable para carpetas --}}
                <div class="bg-white shadow-md rounded-lg p-3">
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Created at:</span> {{ $log->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="flex items-center mb-2">
                        <span class="font-bold text-gray-800 text-base">{{ $log->user_id }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Action:</span> {{ $log->action }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Loggable Type:</span> {{ $log->loggable_type }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Loggable ID:</span> {{ $log->loggable_id }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Data:</span> {{ $log->data }}
                    </div>                              
                </div>
            @endforeach
        </div>

        <div class="mt-6 {{ $logs->lastPage() > 1 ? 'pb-6' : 'pb-1' }}">
        {{ $logs->links() }} {{-- Show pagination links --}}
        </div>            
        @endif

    </div>

@endsection










