@extends('layouts.app')

@section('title', 'Listado de Contraseñas') 

@section('content') 

<div class="container mx-auto md:px-6">

    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 
        @include('partials.header')
    </div>

    <!-- Tabs -->    
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

        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('account.elements', ['uuid' => $uuid ?? '0']) }}" method="GET">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <div class="flex gap-2">
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Search
                        </button>
                        @if(request()->has('search'))
                            <a href="{{ route('account.elements', ['uuid' => $uuid ?? '0']) }}" class="w-full sm:w-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if(!request()->has('search'))
            <h4 class="text-md font-bold mb-4 text-gray-800">Current Directory: {{ $currentDirectory }}</h4>
        @else
            <h4 class="text-md font-bold mb-4 text-gray-800">Resultados de búsqueda para: "{{ request('search') }}"</h4>
        @endif
        

        @if ($elements->isEmpty())
            <p class="text-red-600 pb-4">There are no passwords registered yet.</p>
        @else
            {{-- Big screens (hidden in small ones) --}}
            <div class="hidden sm:block overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                PBKDF2 Iterations
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Modified
                            </th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($elements as $element)
                            @if ($element->element_type_id !== 4)
                            <tr class="hover:bg-gray-50">
                            @else 
                            <tr class="hover:bg-gray-50 cursor-pointer" data-href="{{ route('account.elements', ['uuid' => $element->uuid]) }}">
                            @endif
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800 flex items-center">
                                    @switch($element->element_type_id)
                                        @case(1)
                                            <i class="fas fa-key text-yellow-500 mr-2"></i>
                                            @break
                                        @case(2)
                                            <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                            @break
                                        @case(3)
                                            <i class="fas fa-file text-green-500 mr-2"></i>
                                            @break
                                        @case(4)
                                            <i class="fas fa-folder text-blue-500 mr-2"></i>
                                            @break
                                    @endswitch
                                    <div class="inline-edit-container">
                                        <span class="element-name">{{ $element->key }}</span>
                                        @if(!($element->element_type_id == 4 && $element->key === '../'))
                                        <button type="button" class="text-gray-400 hover:text-gray-600 ml-2 edit-name-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endif
                                        <form action="#" method="POST" class="edit-name-form hidden" data-uuid="{{ $element->uuid }}">
                                            @csrf
                                            <div class="flex items-center">
                                                <input type="text" name="name" value="{{ $element->key }}" class="border rounded px-2 py-1 text-sm" required>
                                                <button type="submit" class="ml-2 text-green-500 hover:text-green-700">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                    @switch($element->element_type_id)
                                        @case(1)
                                            Password
                                            @break
                                        @case(2)
                                            Text
                                            @break
                                        @case(3)
                                            File
                                            @break
                                        @case(4)
                                            Folder
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                    @if ($element->element_type_id !== 4)
                                        {{ $element->iterations }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                    {{ $element->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium action-buttons-cell">                           
                                    @if($element->element_type_id == 1 || $element->element_type_id == 2)
                                    <button type="button"
                                            data-uuid="{{ $element->uuid }}"
                                            data-key="{{ $element->key }}"
                                            data-type="{{ $element->element_type_id }}"
                                            class="text-blue-600 hover:text-blue-900 view-button p-1 rounded-full hover:bg-blue-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @elseif($element->is_file)
                                    <button type="button"
                                            data-uuid="{{ $element->uuid }}"
                                            data-key="{{ $element->key }}"
                                            class="download-button text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    @endif
                                    @if ( (($element->has_children == 0) && ($element->element_type_id == 4) && ($element->key !== '../'))
                                          || ($element->element_type_id != 4))
                                    <form action="{{ route('elements.delete', $element->uuid) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="parent" value="{{ $uuid ?? '0' }}">
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition duration-150 ease-in-out focus:outline-none">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach                        
                    </tbody>
                </table>
            </div>


            {{-- Small screens (hidden in big ones) --}}

                <div class="sm:hidden grid grid-cols-1 gap-3">
                    @foreach ($elements as $element)
                        {{-- Usamos un div principal para la tarjeta que actuará como el elemento clicable para carpetas --}}
                        @if ($element->element_type_id !== 4)
                        <div class="bg-white shadow-md rounded-lg p-3">
                        @else
                        <div class="bg-white shadow-md rounded-lg p-3 cursor-pointer folder-card" data-href="{{ route('account.elements', ['uuid' => $element->uuid]) }}">
                        @endif
                            <div class="flex items-center mb-2">
                                @switch($element->element_type_id)
                                    @case(1)
                                        <i class="fas fa-key text-yellow-500 mr-2 text-lg"></i>
                                        @break
                                    @case(2)
                                        <i class="fas fa-file-alt text-gray-500 mr-2 text-lg"></i>
                                        @break
                                    @case(4)
                                        <i class="fas fa-folder text-blue-500 mr-2 text-lg"></i>
                                        @break
                                    @case(3)
                                        <i class="fas fa-file text-gray-500 mr-2 text-lg"></i>
                                        @break
                                @endswitch
                                <div class="inline-edit-container">
                                    <div class="flex items-center">
                                        <span class="element-name font-bold text-gray-800 text-base">{{ $element->key }}</span>
                                        @if(!($element->element_type_id == 4 && $element->key === '../'))
                                        <button type="button" class="text-gray-400 hover:text-gray-600 ml-2 edit-name-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <form action="#" method="POST" class="edit-name-form hidden mt-2" data-uuid="{{ $element->uuid }}">
                                        @csrf
                                        <div class="flex items-center">
                                            <input type="text" name="name" value="{{ $element->key }}" class="border rounded px-2 py-1 text-sm w-full" required>
                                            <button type="submit" class="ml-2 text-green-500 hover:text-green-700">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-semibold">Type:</span>
                                @switch($element->element_type_id)
                                    @case(1)
                                        Password
                                        @break
                                    @case(2)
                                        Text
                                        @break
                                    @case(3)
                                        File
                                        @break
                                    @case(4)
                                        Folder
                                        @break
                                @endswitch
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                @if ($element->element_type_id !== 4)
                                    <span class="font-semibold">PBKDF2 Iterations:</span> {{ $element->iterations }}
                                @endif
                            </div>                              
                            <div class="text-sm text-gray-600 mb-3">
                                <span class="font-semibold">Last Modified:</span> {{ $element->updated_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="flex justify-end space-x-2 action-buttons-card">
                                @if($element->element_type_id == 1 || $element->element_type_id == 2)                                
                                <button type="button"
                                        data-uuid="{{ $element->uuid }}"
                                        data-key="{{ $element->key }}"
                                        data-type="{{ $element->element_type_id }}"
                                        class="text-blue-600 hover:text-blue-900 view-button px-3 py-1 rounded-md hover:bg-blue-100 transition duration-150 ease-in-out text-sm"
                                        onclick="event.stopPropagation()">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @elseif($element->is_file)
                                <button type="button"
                                        data-uuid="{{ $element->uuid }}"
                                        data-key="{{ $element->key }}"
                                        class="download-button text-green-600 hover:text-green-900 px-3 py-1 rounded-md hover:bg-green-100 transition duration-150 ease-in-out text-sm"
                                        onclick="event.stopPropagation()">
                                    <i class="fas fa-download"></i> Download
                                </button>
                                @endif
                                @if ( (($element->has_children == 0) && ($element->element_type_id == 4) && ($element->key !== '../'))
                                          || ($element->element_type_id != 4))
                                <form action="{{ route('elements.delete', $element->uuid) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');" onclick="event.stopPropagation(); event.stopImmediatePropagation();">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="parent" value="{{ $uuid ?? '0' }}">
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 px-3 py-1 rounded-md hover:bg-red-100 transition duration-150 ease-in-out text-sm focus:outline-none">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            <div class="mt-6 {{ $elements->lastPage() > 1 ? 'pb-6' : 'pb-1' }}">
                {{ $elements->links() }} {{-- Show pagination links --}}
            </div>            
        @endif



    </div>
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h1 class="text-3xl font-bold mb-4 text-gray-800">Add New Element</h1>
            <form id="elementForm" action="{{ route('elements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="element_type_id" class="block text-gray-700 text-sm font-bold mb-2">Type:</label>
                    <select name="element_type_id" id="element_type_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="1" selected>Password</option>
                        <option value="2">Text</option>
                        <option value="3">File</option>
                        <option value="4">Folder</option>
                    </select>
                </div>
                <div class="mb-4">
                     <label for="key" id="keyLabel" class="block text-gray-700 text-sm font-bold mb-2">Element name (key):</label>
                     <input type="text" name="key" id="key" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                 </div>
                
                <!-- File Upload Field -->
                <div class="mb-4 hidden" id="fileFormGroup">
                    <label for="fileInput" class="block text-gray-700 text-sm font-bold mb-2">Select File:</label>
                    <div class="flex items-center">
                        <label class="flex flex-col items-center px-4 py-2 bg-white text-blue-500 rounded-lg border border-blue-500 cursor-pointer hover:bg-blue-50">
                            <span class="text-sm">Choose File</span>
                            <input type="file" id="fileInput" name="file" class="hidden">
                        </label>
                        <span id="fileNameDisplay" class="ml-3 text-sm text-gray-600">No file selected</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Files are encrypted client-side before upload</p>
                </div>
                 <div class="mb-4" id="contentFieldWrapper"> {{-- Wrapper para ocultar/mostrar --}}
                    <div class="flex justify-between items-center mb-2">
                        <label for="passwordPlain" id="contentLabel" class="block text-gray-700 text-sm font-bold">Content:</label>
                        <div id="passwordGeneratorContainer" class="flex items-center space-x-2" style="display: none;">
                            <input type="number" id="passwordLength" min="8" max="100" value="20" class="w-16 px-2 py-1 border rounded text-sm" placeholder="Length">
                            <button type="button" id="generatePasswordBtn" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-2 rounded focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <i class="fas fa-key mr-1"></i>Generate
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="password" id="passwordPlain" name="passwordPlain" class="shadow border rounded w-full py-2 px-3 text-gray-700 pr-10" required>
                        <button type="button" id="togglePasswordVisibility" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <textarea id="passwordPlainTextarea" name="passwordPlainTextarea" class="shadow border rounded w-full py-2 px-3 text-gray-700 hidden mt-2" rows="5"></textarea>
                </div>

                {{-- Campos ocultos para la encriptación, siempre presentes para passwords --}}
                <input type="hidden" name="passwordEncrypted" id="passwordEncrypted">
                <input type="hidden" name="iv">
                <input type="hidden" name="salt">
                <input type="hidden" name="hmac">
                <input type="hidden" name="iterations">
                <input type="hidden" name="parent" value="{{ $uuid }}">

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Element
                    </button>
                </div>
            </form>
        </div>


        {{-- Delete Confirmation Modal --}}
        <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center z-50">
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.332 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Confirm Deletion</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Are you sure you want to delete this element? This action cannot be undone.</p>
                    </div>
                    <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                        <form id="deleteForm" method="POST" action="" class="w-full sm:ml-3 sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                                Delete
                            </button>
                        </form>
                        <button id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- Add encrypted element modal --}}
        <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-[50rem]">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Enter Master Key</h3>
                <input type="password" id="masterKey" class="w-full border rounded p-2 mb-4" 
                    autocomplete="new-password" aria-autocomplete="none" value="">
                <div class="flex justify-end">
                    <button id="cancelModal" class="mr-2 px-4 py-2 text-gray-600">Cancel</button>
                    <button id="confirmEncryption" class="bg-blue-500 text-white px-4 py-2 rounded">Encrypt & Save</button>
                </div>
            </div>
        </div>


        {{-- View encrypted element modal --}}
        <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-[50rem]">
                <h3 class="text-lg font-bold mb-4">View encrypted element</h3>
                <label class="block mb-2 text-sm text-gray-600">Enter master key:</label>
                <input id="viewMasterKeyInput" type="password" class="w-full p-2 border rounded mb-4" placeholder="Master key"
                    autocomplete="new-password" aria-autocomplete="none" value="">

                <button id="decryptBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Decrypt
                </button>

                {{-- Decrypted element display as a read-only input --}}
                <div class="mt-4">
                    <label for="decryptedPasswordInput" class="block mb-2 text-sm text-gray-600">Decrypted Element:</label>
                    <div id="decryptedPasswordInputWrapper" class="flex items-center space-x-2">
                        <input type="text" id="decryptedPasswordInput" 
                               class="w-full p-2 border rounded text-gray-800 break-all bg-gray-50" 
                               readonly>
                        <button id="copyPasswordBtn" 
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded hover:bg-gray-300 transition duration-150 ease-in-out"
                                title="Copy to clipboard">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>
                    {{-- Textarea for text type --}}
                    <div id="decryptedPasswordTextareaWrapper" class="flex items-center space-x-2 hidden">
                        <textarea id="decryptedPasswordTextarea" rows="8" class="w-full p-2 border rounded text-gray-800 break-all bg-gray-50" readonly></textarea>
                        <button id="copyPasswordTextareaBtn" 
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded hover:bg-gray-300 transition duration-150 ease-in-out"
                                title="Copy to clipboard">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div id="countdown" class="mt-2 text-sm text-gray-500"></div>

                <button type="button" class="close-button mt-4 text-sm text-red-500 hover:underline">Close</button>
            </div>
        </div>



@endsection

@section('scripts_extra')
    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" 
          integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />

    {{-- Main JavaScript file for the elements page --}}
    <script type="module">
        // Set the PHP variable in the global scope for the JavaScript module
        window.encryptionIterations = {{ $iterations }};
        
        // Import the main JavaScript file
        import '{{ asset('js/elements.js') }}';
    </script>
@endsection