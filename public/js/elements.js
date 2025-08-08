// Import functions from the utility file
import { encryptData, decryptData, validateBase64, secureWipe, secureWipeMultiple, SecureString } from './encryptionUtils.js';

document.addEventListener('DOMContentLoaded', function () {
    // Delete Confirmation Modal
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

    cancelDeleteButton?.addEventListener('click', function () {
        modal?.classList.add('hidden');
    });

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // --- Add Password (Encryption) Logic ---
    const form = document.getElementById("elementForm");
    const encryptionModal = document.getElementById("modal");
    const masterKeyInput = document.getElementById("masterKey");

    form?.addEventListener("submit", async (e) => {
        // Send the form if it's a folder
        if (document.getElementById("element_type_id")?.value === "4") {
            return;
        }
        
        e.preventDefault();
        encryptionModal?.classList.remove("hidden");
        masterKeyInput?.focus();
    });

    document.getElementById("cancelModal")?.addEventListener("click", () => {
        secureWipe(masterKeyInput);
        encryptionModal?.classList.add("hidden");
    });

    document.getElementById("confirmEncryption")?.addEventListener("click", async () => {
        const elementType = document.getElementById("element_type_id")?.value;
        let plaintext = '';
        let plaintextElement;
        
        switch (elementType) {
            case "1":
                plaintextElement = document.getElementById("passwordPlain");
                plaintext = plaintextElement?.value || '';
                break;
            case "2":
                plaintextElement = document.getElementById("passwordPlainTextarea");
                plaintext = plaintextElement?.value || '';
                break;
        }
        
        const passphrase = masterKeyInput?.value || '';

        if (!passphrase || !plaintext) {
            alert("Both fields are required.");
            return;
        }

        try {
            const { encryptedData, iv, salt, hmac } = await encryptData(plaintext, passphrase, window.encryptionIterations || 100000);

            document.getElementById("passwordEncrypted").value = encryptedData;
            document.querySelector("input[name='iv']").value = iv;
            document.querySelector("input[name='salt']").value = salt;
            document.querySelector("input[name='hmac']").value = hmac;
            document.querySelector("input[name='iterations']").value = window.encryptionIterations || 100000;

            secureWipeMultiple(plaintextElement, masterKeyInput);
            encryptionModal?.classList.add("hidden");
            form.submit();
            
        } catch (error) {
            console.error("Encryption error:", error);
            alert("Error encrypting password. Please try again.");
            secureWipeMultiple(plaintextElement, masterKeyInput);
        }
    });

    // --- Show Password (Decryption) Logic ---
    const viewButtons = document.querySelectorAll('.view-button');
    const viewModal = document.getElementById('viewModal');
    const viewMasterKeyInput = document.getElementById('viewMasterKeyInput');
    const decryptBtn = document.getElementById('decryptBtn');
    const decryptedPasswordInput = document.getElementById('decryptedPasswordInput');        
    const decryptedPasswordTextarea = document.getElementById('decryptedPasswordTextarea');
    const copyPasswordBtn = document.getElementById('copyPasswordBtn');      
    const copyPasswordTextareaBtn = document.getElementById('copyPasswordTextareaBtn');
    const countdownDisplay = document.getElementById('countdown');
    const decryptedPasswordTextareaWrapper = document.getElementById('decryptedPasswordTextareaWrapper');
    const decryptedPasswordInputWrapper = document.getElementById('decryptedPasswordInputWrapper');

    let currentPasswordData = {};
    let countdownTimer;
    let secureDecryptedData = null;

    // Improved cleaning function
    window.closeModal = function() {
        viewModal?.classList.add('hidden');
        
        secureWipeMultiple(
            viewMasterKeyInput,
            decryptedPasswordInput,
            decryptedPasswordTextarea
        );
        
        if (secureDecryptedData) {
            secureDecryptedData.destroy();
            secureDecryptedData = null;
        }
        
        currentPasswordData = {};
        clearInterval(countdownTimer);
        if (countdownDisplay) countdownDisplay.textContent = '';
        if (copyPasswordBtn) copyPasswordBtn.innerHTML = '<i class="far fa-copy"></i>';
        if (copyPasswordTextareaBtn) copyPasswordTextareaBtn.innerHTML = '<i class="far fa-copy"></i>';
    };
    
    // Close modal when clicking outside of it
    viewModal?.addEventListener('click', function(event) {
        if (event.target === viewModal) {
            closeModal();
        }
    });

    // Clean when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && viewModal && !viewModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Clean when window loses focus
    window.addEventListener('blur', function() {
        if (viewModal && !viewModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    viewButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const elementId = button.dataset.id;
            const elementType = button.dataset.type;
            viewModal?.classList.remove('hidden');
            
            if (secureDecryptedData) {
                secureDecryptedData.destroy();
                secureDecryptedData = null;
            }
            
            clearInterval(countdownTimer); 
            if (countdownDisplay) countdownDisplay.textContent = '';
            secureWipeMultiple(
                decryptedPasswordInput,
                decryptedPasswordTextarea,
                viewMasterKeyInput
            );

            // Check if the element is a text type
            if (elementType === '2') {
                decryptedPasswordTextarea?.classList.remove('hidden');
                decryptedPasswordInput?.classList.add('hidden');
                decryptedPasswordTextareaWrapper?.classList.remove('hidden');
                decryptedPasswordInputWrapper?.classList.add('hidden');
            } else {
                decryptedPasswordTextarea?.classList.add('hidden');
                decryptedPasswordInput?.classList.remove('hidden');
                decryptedPasswordTextareaWrapper?.classList.add('hidden');
                decryptedPasswordInputWrapper?.classList.remove('hidden');
            }

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
                if (decryptedPasswordInput) decryptedPasswordInput.value = "❌ Error loading password data.";
                if (decryptedPasswordTextarea) decryptedPasswordTextarea.value = "❌ Error loading password data.";
            }
        });
    });

    if (decryptBtn) {
        decryptBtn.addEventListener('click', async () => {
            const masterKey = viewMasterKeyInput?.value || '';

            if (!masterKey) {
                if (decryptedPasswordInput) decryptedPasswordInput.value = "Please enter the master key.";
                if (decryptedPasswordTextarea) decryptedPasswordTextarea.value = "Please enter the master key.";
                return;
            }

            const { content, iv, salt, hmac, iterations } = currentPasswordData;

            if (!content || !iv || !salt || !hmac) {
                if (decryptedPasswordInput) decryptedPasswordInput.value = "❌ Password data not available. Try again.";
                if (decryptedPasswordTextarea) decryptedPasswordTextarea.value = "❌ Password data not available. Try again.";
                return;
            }

            try {
                const decrypted = await decryptData(content, masterKey, iv, salt, hmac, iterations);
                
                secureDecryptedData = new SecureString(decrypted, 30000); // 30 seconds maximum
                
                if (decryptedPasswordInput) decryptedPasswordInput.value = secureDecryptedData.getValue();
                if (decryptedPasswordTextarea) decryptedPasswordTextarea.value = secureDecryptedData.getValue();
                
                secureWipe(viewMasterKeyInput);

                clearInterval(countdownTimer); 
                let timeLeft = 30;
                if (countdownDisplay) countdownDisplay.textContent = `This will close in ${timeLeft} seconds.`;
                
                countdownTimer = setInterval(() => {
                    timeLeft--;
                    if (countdownDisplay) countdownDisplay.textContent = `This will close in ${timeLeft} seconds.`;
                    if (timeLeft <= 0) {
                        closeModal();
                    }
                }, 1000);
                
            } catch (e) {
                console.error("Error during decryption:", e);
                if (decryptedPasswordInput) decryptedPasswordInput.value = "❌ Decryption failed: " + e.message;
                if (decryptedPasswordTextarea) decryptedPasswordTextarea.value = "❌ Decryption failed: " + e.message;
                
                secureWipe(viewMasterKeyInput);
                clearInterval(countdownTimer); 
                if (countdownDisplay) countdownDisplay.textContent = '';
            }
        });
    }

    // Copy to clipboard functionality
    if (copyPasswordBtn) {
        copyPasswordBtn.addEventListener('click', async () => {
            try {
                let textToCopy = '';
                if (secureDecryptedData && !secureDecryptedData._destroyed) {
                    textToCopy = secureDecryptedData.getValue();
                } else if (decryptedPasswordInput) {
                    textToCopy = decryptedPasswordInput.value;
                }
                
                if (textToCopy) {
                    await navigator.clipboard.writeText(textToCopy);
                    copyPasswordBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        copyPasswordBtn.innerHTML = '<i class="far fa-copy"></i>';
                    }, 2000);
                }
            } catch (err) {
                console.error('Failed to copy text: ', err);
                alert('Failed to copy password. Please try again or copy manually.');
            }
        });
    }

    if (copyPasswordTextareaBtn) {
        copyPasswordTextareaBtn.addEventListener('click', async () => {
            try {
                let textToCopy = '';
                if (secureDecryptedData && !secureDecryptedData._destroyed) {
                    textToCopy = secureDecryptedData.getValue();
                } else if (decryptedPasswordTextarea) {
                    textToCopy = decryptedPasswordTextarea.value;
                }
                
                if (textToCopy) {
                    await navigator.clipboard.writeText(textToCopy);
                    copyPasswordTextareaBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        copyPasswordTextareaBtn.innerHTML = '<i class="far fa-copy"></i>';
                    }, 2000);
                }
            } catch (err) {
                console.error('Failed to copy text: ', err);
                alert('Failed to copy password. Please try again or copy manually.');
            }
        });
    }

    // --- Update form fields depending on the type of element ---
    const elementTypeSelect = document.getElementById('element_type_id');
    const keyLabel = document.getElementById('keyLabel');
    const contentFieldWrapper = document.getElementById('contentFieldWrapper');
    const passwordPlainInput = document.getElementById('passwordPlain');
    const passwordPlainTextarea = document.getElementById('passwordPlainTextarea');

    function updateFormFields() {
        if (!elementTypeSelect) return;
        
        const selectedValue = elementTypeSelect.value;

        // Type 1: Password
        if (selectedValue === '1') {
            if (keyLabel) keyLabel.textContent = 'Element name (key):';
            if (contentFieldWrapper) contentFieldWrapper.classList.remove('hidden');
            if (passwordPlainInput) {
                passwordPlainInput.setAttribute('required', 'required');
                passwordPlainInput.classList.remove('hidden');
            }
            if (passwordPlainTextarea) {
                passwordPlainTextarea.removeAttribute('required');
                passwordPlainTextarea.classList.add('hidden');
            }
        }
        // Type 2: Text
        else if (selectedValue === '2') {
            if (keyLabel) keyLabel.textContent = 'Element name (key):';
            if (contentFieldWrapper) contentFieldWrapper.classList.remove('hidden');
            if (passwordPlainInput) {
                passwordPlainInput.removeAttribute('required');
                passwordPlainInput.classList.add('hidden');
            }
            if (passwordPlainTextarea) {
                passwordPlainTextarea.setAttribute('required', 'required');
                passwordPlainTextarea.classList.remove('hidden');
            }
        }
        // Type 4: Folder
        else if (selectedValue === '4') {
            if (keyLabel) keyLabel.textContent = 'Folder name:';
            if (contentFieldWrapper) contentFieldWrapper.classList.add('hidden');
            if (passwordPlainInput) {
                passwordPlainInput.removeAttribute('required');
            }
            if (passwordPlainTextarea) {
                passwordPlainTextarea.removeAttribute('required');
                passwordPlainTextarea.classList.add('hidden');
            }
        }
    }

    // Initialize form fields
    if (elementTypeSelect) {
        updateFormFields();
        elementTypeSelect.addEventListener('change', updateFormFields);
    }

    // Navigate through folders - Rows
    const rows = document.querySelectorAll('tbody tr[data-href]');
    rows.forEach(row => {
        row.addEventListener('click', function(event) {
            if (event.target.closest('.action-buttons-cell')) {
                return;
            }
            const url = this.dataset.href;
            if (url) {
                window.location.href = url;
            }
        });
    });

    // Navigate through folders - Cards
    const folderCards = document.querySelectorAll('.folder-card[data-href]');
    folderCards.forEach(card => {
        card.addEventListener('click', function(event) {
            if (event.target.closest('.action-buttons-card')) {
                return;
            }
            const url = this.dataset.href;
            if (url) {
                window.location.href = url;
            }
        });
    });
});
