@extends('layouts.app')

@section('title', 'Listado de Contraseñas') 

@section('content') 

<div class="container mx-auto px-6">

    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 
        @include('partials.header')
    </div>

    <!-- Tabs -->
    
    @include('partials.pestanyas')
    

    <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 
        
        <h1 class="text-3xl font-bold mb-6 text-gray-800">List of Elements</h1>
    
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
                                        @case(4)
                                            <i class="fas fa-folder text-blue-500 mr-2"></i>
                                            @break
                                    @endswitch
                                    {{ $element->key }}
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
                                    {{ $element->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium action-buttons-cell">                           
                                    @if ($element->element_type_id !== 4)
                                    <button type="button"
                                            data-id="{{ $element->uuid }}"
                                            class="text-blue-600 hover:text-blue-900 view-button p-1 rounded-full hover:bg-blue-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endif
                                    <button type="button"
                                            data-id="{{ $element->uuid }}"
                                            class="text-red-600 hover:text-red-900 delete-button p-1 rounded-full hover:bg-red-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
                                    @case(4)
                                        <i class="fas fa-folder text-blue-500 mr-2 text-lg"></i>
                                        @break
                                    {{-- Añade más casos aquí para otros tipos de elementos si los tienes --}}
                                    @case(2)
                                        <i class="fas fa-file-alt text-gray-500 mr-2 text-lg"></i> {{-- Ejemplo para tipo Text --}}
                                        @break
                                    @case(3)
                                        <i class="fas fa-file text-gray-500 mr-2 text-lg"></i> {{-- Ejemplo para tipo File --}}
                                        @break
                                @endswitch
                                <span class="font-bold text-gray-800 text-base">{{ $element->key }}</span>
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
                            <div class="text-sm text-gray-600 mb-3">
                                <span class="font-semibold">Last Modified:</span> {{ $element->updated_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="flex justify-end space-x-2 action-buttons-card"> {{-- Nueva clase para la celda de botones --}}
                                @if ($element->element_type_id !== 4) {{-- Los elementos que no son carpetas tienen botón de "View" --}}
                                <button type="button"
                                        data-id="{{ $element->uuid }}"
                                        class="text-blue-600 hover:text-blue-900 view-button px-3 py-1 rounded-md hover:bg-blue-100 transition duration-150 ease-in-out text-sm"
                                        onclick="event.stopPropagation()"> {{-- Detener propagación --}}
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @endif
                                <button type="button"
                                        data-id="{{ $element->uuid }}"
                                        class="text-red-600 hover:text-red-900 delete-button px-3 py-1 rounded-md hover:bg-red-100 transition duration-150 ease-in-out text-sm"
                                        onclick="event.stopPropagation()"> {{-- Detener propagación --}}
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            <div class="mt-6 pb-6">
                {{ $elements->links() }} {{-- Show pagination links --}}
            </div>            
        @endif



    </div>
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h1 class="text-3xl font-bold mb-4 text-gray-800">Add New Element</h1>
            <form id="elementForm" action="{{ route('elements.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="element_type_id" class="block text-gray-700 text-sm font-bold mb-2">Type:</label>
                    <select name="element_type_id" id="element_type_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="1" selected>Password</option>
                        <option value="4">Folder</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="key" id="keyLabel" class="block text-gray-700 text-sm font-bold mb-2">Element name (key):</label>
                    <input type="text" name="key" id="key" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div class="mb-4" id="contentFieldWrapper"> {{-- Wrapper para ocultar/mostrar --}}
                    <label for="passwordPlain" id="contentLabel" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
                    <input type="text" id="passwordPlain" name="content" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
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
                    <div class="flex items-center space-x-2">
                        <input type="text" id="decryptedPasswordInput" 
                               class="w-full p-2 border rounded text-gray-800 break-all bg-gray-50" 
                               readonly>
                        <button id="copyPasswordBtn" 
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded hover:bg-gray-300 transition duration-150 ease-in-out"
                                title="Copy to clipboard">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div id="countdown" class="mt-2 text-sm text-gray-500"></div>

                <button onclick="closeModal()" class="mt-4 text-sm text-red-500 hover:underline">Close</button>
            </div>
        </div>



@endsection

@section('scripts_extra')
    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <script type="module">
        // Import functions from the utility file
        import { encryptData, decryptData, validateBase64 } from '{{ asset('js/encryptionUtils.js') }}';

        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
            const modal = document.getElementById('deleteConfirmationModal');
            const cancelDeleteButton = document.getElementById('cancelDelete');
            const deleteForm = document.getElementById('deleteForm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const elementId = this.dataset.id;
                    deleteForm.action = `/myaccount/elements/${elementId}`;
                    modal.classList.remove('hidden');
                });
            });

            cancelDeleteButton.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        // --- Add Password (Encryption) Logic ---
        const form = document.getElementById("elementForm");
        const encryptionModal = document.getElementById("modal"); // Renamed to avoid conflict
        const masterKeyInput = document.getElementById("masterKey");

        form.addEventListener("submit", (e) => {
            // Send the form if its a folder
            if (document.getElementById("element_type_id").value === "4") {
                form.submit();
                return;
            }
            // Prevent default form submission
            e.preventDefault();
            encryptionModal.classList.remove("hidden");
            masterKeyInput.focus();
        });

        document.getElementById("cancelModal").addEventListener("click", () => {
            encryptionModal.classList.add("hidden");
            masterKeyInput.value = '';
        });

        document.getElementById("confirmEncryption").addEventListener("click", async () => {
            const plaintext = document.getElementById("passwordPlain").value;
            const passphrase = masterKeyInput.value;

            if (!passphrase || !plaintext) {
                alert("Both fields are required.");
                return;
            }

            try {
                const { encryptedData, iv, salt, hmac } = await encryptData(plaintext, passphrase, {{ $iterations }});

                document.getElementById("passwordEncrypted").value = encryptedData;
                document.querySelector("input[name='iv']").value = iv;
                document.querySelector("input[name='salt']").value = salt;
                document.querySelector("input[name='hmac']").value = hmac;
                document.querySelector("input[name='iterations']").value = {{ $iterations }};

                // Clean plaintext input and master key
                document.getElementById("passwordPlain").value = '';
                masterKeyInput.value = '';
                encryptionModal.classList.add("hidden");
                form.submit();
            } catch (error) {
                console.error("Encryption error:", error);
                alert("Error encrypting password. Please try again.");
            }
        });

        // --- Show Password (Decryption) Logic ---
        const viewButtons = document.querySelectorAll('.view-button');
        const viewModal = document.getElementById('viewModal');
        const viewMasterKeyInput = document.getElementById('viewMasterKeyInput');
        const decryptBtn = document.getElementById('decryptBtn');
        const decryptedPasswordInput = document.getElementById('decryptedPasswordInput'); 
        const copyPasswordBtn = document.getElementById('copyPasswordBtn');       
        const countdownDisplay = document.getElementById('countdown'); 

        let currentPasswordData = {};
        let countdownTimer;

        // Clean everything when closing modal
        window.closeModal = function() { // Make it global so onclick in HTML can call it
            viewModal.classList.add('hidden');
            viewMasterKeyInput.value = '';
            currentPasswordData = {};
            clearInterval(countdownTimer);
            countdownDisplay.textContent = '';
            copyPasswordBtn.innerHTML = '<i class="far fa-copy"></i>';
            decryptedPasswordInput.value = '';
            viewMasterKeyInput.value = '';
        }
        
        // Close modal when clicking outside of it
        viewModal.addEventListener('click', function(event) {
            if (event.target === viewModal) {
                closeModal();
            }
        });

        viewButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const elementId = button.dataset.id;
                viewModal.classList.remove('hidden');
                clearInterval(countdownTimer); 
                countdownDisplay.textContent = '';
                decryptedPasswordInput.value = ''; // Clear previous decrypted password
                viewMasterKeyInput.value = ''; // Clear master key input

                try {
                    const response = await fetch(`/myaccount/elements/get/${elementId}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    currentPasswordData = {
                        content: data.content,
                        iv: data.iv,
                        salt: data.salt,
                        hmac: data.hmac,
                        iterations: data.iterations
                    };

                    if (!validateBase64(currentPasswordData.content) || !validateBase64(currentPasswordData.iv) || !validateBase64(currentPasswordData.salt)) {
                        throw new Error("Invalid base64 data received.");
                    }

                } catch (error) {
                    console.error('Error fetching password data:', error);
                    decryptedPasswordInput.value = "❌ Error loading password data.";
                }
            });
        });

        decryptBtn.addEventListener('click', async () => {
            const masterKey = viewMasterKeyInput.value;

            if (!masterKey) {
                decryptedPasswordInput.value = "Please enter the master key.";
                return;
            }

            const { content, iv, salt, hmac, iterations } = currentPasswordData;

            if (!content || !iv || !salt || !hmac) {
                decryptedPasswordInput.value = "❌ Password data not available. Try again.";
                return;
            }

            try {
                const decrypted = await decryptData(content, masterKey, iv, salt, hmac, iterations);
                decryptedPasswordInput.value = decrypted;
                viewMasterKeyInput.value = ''; // Clear master key input
                if (window.gc) window.gc(); // Force garbage collection if available

                clearInterval(countdownTimer); 
                let timeLeft = 10;
                countdownDisplay.textContent = `This will close in ${timeLeft} seconds.`;
                

                countdownTimer = setInterval(() => {
                    timeLeft--;
                    countdownDisplay.textContent = `This will close in ${timeLeft} seconds.`;
                    if (timeLeft <= 0) {
                        closeModal();
                    }
                }, 1000);
            } catch (e) {
                console.error("Error during decryption:", e);
                decryptedPasswordInput.value = "❌ Decryption failed: " + e.message;
                clearInterval(countdownTimer); 
                countdownDisplay.textContent = '';
            }
        });

        copyPasswordBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(decryptedPasswordInput.value);
                copyPasswordBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    copyPasswordBtn.innerHTML = '<i class="far fa-copy"></i>';
                }, 2000);
            } catch (err) {
                console.error('Failed to copy text: ', err);
                alert('Failed to copy password. Please try again or copy manually.');
            }
        });
    </script>


    <script>
        // --- Update form fields depending on the type of element ---
        document.addEventListener('DOMContentLoaded', function() {
            const elementTypeSelect = document.getElementById('element_type_id');
            const keyLabel = document.getElementById('keyLabel');
            const contentFieldWrapper = document.getElementById('contentFieldWrapper');
            const passwordPlainInput = document.getElementById('passwordPlain');

            function updateFormFields() {
                const selectedValue = elementTypeSelect.value;

                // Type 1: Password
                if (selectedValue === '1') {
                    keyLabel.textContent = 'Element name (key):';
                    contentFieldWrapper.classList.remove('hidden'); // Show content field
                    passwordPlainInput.setAttribute('required', 'required'); // Make it required
                    passwordPlainInput.name = 'passwordPlain'; // Ensure the name is 'passwordPlain'
                    passwordPlainInput.id = 'passwordPlain'; // Restore the ID if necessary
                }
                // Type 4: Folder
                else if (selectedValue === '4') {
                    keyLabel.textContent = 'Folder name:'; // Change the label text
                    contentFieldWrapper.classList.add('hidden'); // Hide the content field
                    passwordPlainInput.removeAttribute('required'); // Not required
                    passwordPlainInput.name = ''; // Remove the 'name' attribute so it's not sent to the server
                    passwordPlainInput.value = ''; // Clear the value to avoid residual data
                }
            }

            // Execute the function when the page loads to set the initial state
            updateFormFields();

            // Listen for changes in the type selector
            elementTypeSelect.addEventListener('change', updateFormFields);

        
            // ********** Navigate through folders **********
            // Select all rows that have the data-href attribute
            const rows = document.querySelectorAll('tbody tr[data-href]');

            rows.forEach(row => {
                row.addEventListener('click', function(event) {
                    // Check if the click came from an action button
                    // event.target is the element that was clicked
                    // event.currentTarget is the element that the event listener was attached to (the row in this case)
                    if (event.target.closest('.action-buttons-cell')) {
                        // If the click was inside the action cell, do nothing
                        return;
                    }

                    // If it wasn't in the action cell, navigate to the URL
                    const url = this.dataset.href;
                    if (url) {
                        window.location.href = url;
                    }
                });
            });


            const folderCards = document.querySelectorAll('.folder-card[data-href]');

            folderCards.forEach(card => {
                card.addEventListener('click', function(event) {
                    // Verificamos si el clic provino de un botón de acción dentro de la tarjeta
                    if (event.target.closest('.action-buttons-card')) {
                        return; // Si sí, no hacemos nada y dejamos que el botón maneje su propio evento
                    }

                    // Si no fue en los botones, navegamos a la URL de la carpeta
                    const url = this.dataset.href;
                    if (url) {
                        window.location.href = url;
                    }
                });
            });


        });




    </script>

@endsection