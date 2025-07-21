@extends('layouts.app')

@section('title', 'Listado de Contraseñas') 

@section('content') 

    @include('partials.header')

    @include('partials.pestanyas')
        
    <div class="container mx-auto">
        

    <button id="test-encryption" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Test Encryption
    </button>

        <h1 class="text-3xl font-bold mb-6 text-gray-800 pt-6">List of Passwords</h1>
    
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
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none';">
                        <title>Cerrar</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697L8.303 10l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif



        @if ($passwords->isEmpty())
            <p class="text-red-600 pb-4">There are no passwords registered yet.</p>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Key
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Updated
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"> {{-- Added for actions --}}
                                Actions
                            </th>                            
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($passwords as $password)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $password->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $password->user->name ?? 'N/A' }} 
                                    {{-- Muestra el nombre del usuario, o 'N/A' si no se encuentra --}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $password->key }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $password->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $password->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button"
                                            data-id="{{ $password->id }}"
                                            data-content="{{ $password->content }}"
                                            data-iv="{{ $password->iv }}"
                                            data-salt="{{ $password->salt }}"
                                            class="text-blue-600 hover:text-blue-900 view-button p-2 rounded-full hover:bg-blue-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="button"
                                            data-id="{{ $password->id }}"
                                            class="text-red-600 hover:text-red-900 delete-button p-2 rounded-full hover:bg-red-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-trash-alt"></i> {{-- Font Awesome delete icon --}}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pb-6">
                {{ $passwords->links() }} {{-- Show pagination links --}}
            </div>
        @endif


        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Password</h2>
            <form id="passwordForm" action="{{ route('passwords.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="key" class="block text-gray-700 text-sm font-bold mb-2">Key (name to identify the site & account):</label>
                    <input type="text" name="key" id="key" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Site Password:</label>
                    <input type="text" id="passwordPlain" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                </div>

                <input type="hidden" name="passwordEncrypted" id="passwordEncrypted">
                <input type="hidden" name="iv">
                <input type="hidden" name="salt">

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Password
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
                        <p class="text-sm text-gray-500">Are you sure you want to delete this password? This action cannot be undone.</p>
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



        <!-- Add Password Modal -->
        <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Enter Master Key</h3>
                <input type="password" id="masterKey" class="w-full border rounded p-2 mb-4" 
                    autocomplete="new-password" aria-autocomplete="none" value="">
                <div class="flex justify-end">
                    <button id="cancelModal" class="mr-2 px-4 py-2 text-gray-600">Cancel</button>
                    <button id="confirmEncryption" class="bg-blue-500 text-white px-4 py-2 rounded">Encrypt & Save</button>
                </div>
            </div>
        </div>

        <!-- Show Password Modal -->
        <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">View password</h3>
                <label class="block mb-2 text-sm text-gray-600">Enter master key:</label>
                <input id="viewMasterKeyInput" type="password" class="w-full p-2 border rounded mb-4" placeholder="Master key"
                    autocomplete="new-password" aria-autocomplete="none" value="">

                <button id="decryptBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Decrypt
                </button>

                <div id="decryptedPassword" class="mt-4 text-gray-800 break-all"></div>

                <button onclick="closeModal()" class="mt-4 text-sm text-red-500 hover:underline">Close</button>
            </div>
        </div>


    </div>


@endsection



@section('scripts_extra')
    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" xintegrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
            const modal = document.getElementById('deleteConfirmationModal');
            const cancelDeleteButton = document.getElementById('cancelDelete');
            const deleteForm = document.getElementById('deleteForm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const passwordId = this.dataset.id;
                    // Update the form action to the correct delete route
                    // Assuming your delete route is something like /passwords/{id}
                    deleteForm.action = `/myaccount/passwords/${passwordId}`; // Adjust this route if necessary
                    modal.classList.remove('hidden');
                });
            });

            cancelDeleteButton.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            // Close modal when clicking outside of it
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>


    <script>
        const form = document.getElementById("passwordForm");
        const modal = document.getElementById("modal");
        const masterKeyInput = document.getElementById("masterKey");

        form.addEventListener("submit", (e) => {
            e.preventDefault(); // prevenimos envío automático
            modal.classList.remove("hidden");
            masterKeyInput.focus();
        });

        document.getElementById("cancelModal").addEventListener("click", () => {
            modal.classList.add("hidden");
            masterKeyInput.value = '';
        });

        document.getElementById("confirmEncryption").addEventListener("click", async () => {
            const plaintext = document.getElementById("passwordPlain").value;
            const passphrase = masterKeyInput.value;

            if (!passphrase || !plaintext) return alert("Both fields are required");

            const salt = crypto.getRandomValues(new Uint8Array(16));
            const iv = crypto.getRandomValues(new Uint8Array(12));

            const keyMaterial = await crypto.subtle.importKey(
                "raw",
                new TextEncoder().encode(passphrase),
                { name: "PBKDF2" },
                false,
                ["deriveKey"]
            );

            const key = await crypto.subtle.deriveKey(
                {
                    name: "PBKDF2",
                    salt: salt,
                    iterations: 100000,
                    hash: "SHA-256"
                },
                keyMaterial,
                { name: "AES-GCM", length: 256 },
                false,
                ["encrypt"]
            );

            const encrypted = await crypto.subtle.encrypt(
                { name: "AES-GCM", iv: iv },
                key,
                new TextEncoder().encode(plaintext)
            );

            // Codificar a base64
            const toBase64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf)));

            document.getElementById("passwordEncrypted").value = toBase64(encrypted);
            document.querySelector("input[name='iv']").value = toBase64(iv);
            document.querySelector("input[name='salt']").value = toBase64(salt);

            // Limpiar input plano
            document.getElementById("passwordPlain").value = '';
            masterKeyInput.value = '';
            modal.classList.add("hidden");
            form.submit();
        });
    </script>





    <!-- Show Password -->
    <script>
        const viewButtons = document.querySelectorAll('.view-button');
        const viewModal = document.getElementById('viewModal');
        const viewMasterKeyInput = document.getElementById('viewMasterKeyInput');
        const decryptBtn = document.getElementById('decryptBtn');
        const decryptedPassword = document.getElementById('decryptedPassword');

        let currentContent, currentIv, currentSalt;

        function closeModal() {
            viewModal.classList.add('hidden');
            decryptedPassword.textContent = '';
            viewMasterKeyInput.value = '';
        }

        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentContent = button.dataset.content;
                currentIv = button.dataset.iv;
                currentSalt = button.dataset.salt;
                viewModal.classList.remove('hidden');
            });
        });

        decryptBtn.addEventListener('click', async () => {
            const masterKey = viewMasterKeyInput.value;

            try {
                const decrypted = await decryptData(currentContent, masterKey, currentIv, currentSalt);
                decryptedPassword.textContent = `Contraseña: ${decrypted}`;
            } catch (e) {
                decryptedPassword.textContent = "❌ Clave incorrecta o datos corruptos.";
            }
        });

        async function decryptData(ciphertextB64, masterKey, ivB64, saltB64) {
            const encoder = new TextEncoder();
            const ciphertext = Uint8Array.from(atob(ciphertextB64), c => c.charCodeAt(0));
            const iv = Uint8Array.from(atob(ivB64), c => c.charCodeAt(0));
            const salt = Uint8Array.from(atob(saltB64), c => c.charCodeAt(0));
            const keyMaterial = await crypto.subtle.importKey(
                "raw",
                encoder.encode(masterKey),
                { name: "PBKDF2" },
                false,
                ["deriveKey"]
            );
            const key = await crypto.subtle.deriveKey(
                {
                    name: "PBKDF2",
                    salt: salt,
                    iterations: 100000,
                    hash: "SHA-256"
                },
                keyMaterial,
                { name: "AES-GCM", length: 256 },
                false,
                ["decrypt"]
            );
            const decrypted = await crypto.subtle.decrypt(
                { name: "AES-GCM", iv: iv },
                key,
                ciphertext
            );
            return new TextDecoder().decode(decrypted);
        }


    </script>




@endsection
