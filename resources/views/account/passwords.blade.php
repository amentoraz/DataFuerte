@extends('layouts.app')

@section('title', 'Listado de Contraseñas') 

@section('content') 

    @include('partials.header')

    @include('partials.pestanyas')
        
    <div class="container mx-auto">
        
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
            {{-- Big screens (hidden in small ones) --}}
            <div class="hidden sm:block overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Key
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Updated
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($passwords as $password)
                            <tr>
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
                                            class="text-blue-600 hover:text-blue-900 view-button p-2 rounded-full hover:bg-blue-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button"
                                            data-id="{{ $password->id }}"
                                            class="text-red-600 hover:text-red-900 delete-button p-2 rounded-full hover:bg-red-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Small screens (hidden in big ones) --}}
            <div class="sm:hidden grid grid-cols-1 gap-4">
                @foreach ($passwords as $password)
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-800">Key:</span>
                            <span class="text-gray-600">{{ $password->key }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-gray-800">Created:</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">                            
                            <span class="text-gray-600">{{ $password->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-gray-800">Last Updated:</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">                                
                            <span class="text-gray-600">{{ $password->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button"
                                    data-id="{{ $password->id }}"
                                    class="text-blue-600 hover:text-blue-900 view-button p-2 rounded-full hover:bg-blue-100 transition duration-150 ease-in-out">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button type="button"
                                    data-id="{{ $password->id }}"
                                    class="text-red-600 hover:text-red-900 delete-button p-2 rounded-full hover:bg-red-100 transition duration-150 ease-in-out">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                    </div>
                @endforeach
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
                <input type="hidden" name="hmac">

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

        <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-[50rem]">
                <h3 class="text-lg font-bold mb-4">View password</h3>
                <label class="block mb-2 text-sm text-gray-600">Enter master key:</label>
                <input id="viewMasterKeyInput" type="password" class="w-full p-2 border rounded mb-4" placeholder="Master key"
                    autocomplete="new-password" aria-autocomplete="none" value="">

                <button id="decryptBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Decrypt
                </button>

                {{-- Decrypted password display as a read-only input --}}
                <div class="mt-4">
                    <label for="decryptedPasswordInput" class="block mb-2 text-sm text-gray-600">Decrypted Password:</label>
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
                    const passwordId = this.dataset.id;
                    deleteForm.action = `/myaccount/passwords/${passwordId}`;
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
        const form = document.getElementById("passwordForm");
        const encryptionModal = document.getElementById("modal"); // Renamed to avoid conflict
        const masterKeyInput = document.getElementById("masterKey");

        form.addEventListener("submit", (e) => {
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
                const { encryptedData, iv, salt, hmac } = await encryptData(plaintext, passphrase);

                document.getElementById("passwordEncrypted").value = encryptedData;
                document.querySelector("input[name='iv']").value = iv;
                document.querySelector("input[name='salt']").value = salt;
                document.querySelector("input[name='hmac']").value = hmac;

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
                const passwordId = button.dataset.id;
                viewModal.classList.remove('hidden');
                clearInterval(countdownTimer); 
                countdownDisplay.textContent = '';
                decryptedPasswordInput.value = ''; // Clear previous decrypted password
                viewMasterKeyInput.value = ''; // Clear master key input

                try {
                    const response = await fetch(`/myaccount/passwords/${passwordId}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    currentPasswordData = {
                        content: data.content,
                        iv: data.iv,
                        salt: data.salt,
                        hmac: data.hmac
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

            const { content, iv, salt, hmac } = currentPasswordData;

            if (!content || !iv || !salt || !hmac) {
                decryptedPasswordInput.value = "❌ Password data not available. Try again.";
                return;
            }

            try {
                const decrypted = await decryptData(content, masterKey, iv, salt, hmac);
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
@endsection