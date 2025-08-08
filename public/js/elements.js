// Import functions from the utility file
import { encryptData, decryptData, validateBase64, secureWipe, secureWipeMultiple, SecureString } from './encryptionUtils.js';
import { encryptFile, decryptFile, downloadDecryptedFile } from './fileEncryption.js';

document.addEventListener('DOMContentLoaded', function () {
    // --- General Setup ---
    const fileInput = document.getElementById("fileInput");
    const fileNameDisplay = document.getElementById("fileNameDisplay");
    const elementForm = document.getElementById("elementForm");
    const elementTypeSelect = document.getElementById('element_type_id');

    // --- Delete Confirmation Modal ---
    const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
    const cancelDeleteButton = document.getElementById('cancelDelete');
    const deleteForm = document.getElementById('deleteForm');

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            deleteForm.action = `/myaccount/elements/${this.dataset.id}`;
            deleteConfirmationModal.classList.remove('hidden');
        });
    });

    cancelDeleteButton?.addEventListener('click', () => deleteConfirmationModal?.classList.add('hidden'));
    deleteConfirmationModal?.addEventListener('click', (event) => { 
        if (event.target === deleteConfirmationModal) deleteConfirmationModal.classList.add('hidden'); 
    });

    // --- Create/Encrypt Modal ---
    const encryptionModal = document.getElementById("modal");
    const masterKeyInput = document.getElementById("masterKey");
    const cancelModalButton = document.getElementById("cancelModal");
    const confirmEncryptionButton = document.getElementById("confirmEncryption");

    elementForm?.addEventListener("submit", function (e) {
        const elementType = elementTypeSelect?.value;
        if (elementType === "4") return; // Allow normal submission for folders
        e.preventDefault();
        if (elementType === "3" && (!fileInput || fileInput.files.length === 0)) {
            return alert("Please select a file to upload");
        }
        encryptionModal?.classList.remove("hidden");
        masterKeyInput?.focus();
    });

    cancelModalButton?.addEventListener("click", () => {
        secureWipe(masterKeyInput);
        encryptionModal?.classList.add("hidden");
    });

    confirmEncryptionButton?.addEventListener("click", async () => {
        const masterKey = masterKeyInput?.value || '';
        if (!masterKey) return alert('Please enter your master key');

        const originalBtnText = confirmEncryptionButton.innerHTML;
        confirmEncryptionButton.disabled = true;
        confirmEncryptionButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const elementType = elementTypeSelect?.value;
            const iterations = window.encryptionIterations || 100000;

            if (elementType === '3') { // File
                const file = fileInput?.files[0];
                if (!file) throw new Error('Please select a file to upload');

                const { encryptedFile, iv, salt, hmac } = await encryptFile(file, masterKey, iterations);
                const formData = new FormData(elementForm);
                formData.set('file', encryptedFile, file.name);
                formData.set('file_iv', iv);
                formData.set('file_salt', salt);
                formData.set('file_hmac', hmac);
                formData.set('iterations', iterations);

                const response = await fetch(elementForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else if (response.ok) {
                    const result = await response.json();
                    window.location.href = result.redirect || '/myaccount/elements';
                } else {
                    const errorResult = await response.json();
                    throw new Error(errorResult.message || 'Failed to upload file');
                }

            } else { // Password or Text
                const plaintextElement = elementType === '1' ? document.getElementById('passwordPlain') : document.getElementById('passwordPlainTextarea');
                const plaintext = plaintextElement?.value || '';
                if (!plaintext) throw new Error('Please enter content to encrypt');

                const { encryptedData, iv, salt, hmac } = await encryptData(plaintext, masterKey, iterations);
                document.getElementById("passwordEncrypted").value = encryptedData;
                document.querySelector("input[name='iv']").value = iv;
                document.querySelector("input[name='salt']").value = salt;
                document.querySelector("input[name='hmac']").value = hmac;
                document.querySelector("input[name='iterations']").value = iterations;

                secureWipe(plaintextElement);
                elementForm.submit();
            }
        } catch (error) {
            console.error("Process error:", error);
            alert(error.message || "An error occurred. Please try again.");
        } finally {
            confirmEncryptionButton.disabled = false;
            confirmEncryptionButton.innerHTML = originalBtnText;
        }
    });

    // --- View/Download/Decrypt Modal ---
    const viewModal = document.getElementById('viewModal');
    const viewModalTitle = document.getElementById('viewModalTitle');
    const viewMasterKeyInput = document.getElementById('viewMasterKeyInput');
    const decryptBtn = document.getElementById('decryptBtn');
    const decryptedPasswordInput = document.getElementById('decryptedPasswordInput');
    const decryptedPasswordTextarea = document.getElementById('decryptedPasswordTextarea');
    const copyPasswordBtn = document.getElementById('copyPasswordBtn');
    const copyPasswordTextareaBtn = document.getElementById('copyPasswordTextareaBtn');
    const decryptedPasswordInputWrapper = document.getElementById('decryptedPasswordInputWrapper');
    const decryptedPasswordTextareaWrapper = document.getElementById('decryptedPasswordTextareaWrapper');
    const countdownDisplay = document.getElementById('countdownDisplay');
    
    let currentElementData = {};
    let countdownTimer;
    let secureDecryptedData = null;

    const closeModal = () => {
        if (viewModal) viewModal.classList.add('hidden');
        if (secureDecryptedData) secureDecryptedData.destroy();
        secureDecryptedData = null;
        clearInterval(countdownTimer);
        secureWipeMultiple(decryptedPasswordInput, decryptedPasswordTextarea, viewMasterKeyInput);
    };

    viewModal?.addEventListener('click', (event) => { if (event.target === viewModal) closeModal(); });
    viewModal?.querySelector('.close-button')?.addEventListener('click', () => closeModal());
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !viewModal.classList.contains('hidden')) closeModal(); });

    const startCountdown = (duration) => {
        let timeLeft = duration;
        if (countdownDisplay) {
            countdownDisplay.textContent = `Closing in ${timeLeft}s`;
            countdownDisplay.classList.remove('hidden');
        }
        clearInterval(countdownTimer);
        countdownTimer = setInterval(() => {
            timeLeft--;
            if (countdownDisplay) countdownDisplay.textContent = `Closing in ${timeLeft}s`;
            if (timeLeft <= 0) closeModal();
        }, 1000);
    };

    const setupActionModal = (button, action) => {
        currentElementData = { uuid: button.dataset.uuid, type: button.dataset.type, key: button.dataset.key };
        const isDownload = action === 'download';
        if (viewModalTitle) viewModalTitle.textContent = isDownload ? `Decrypt & Download: ${currentElementData.key}` : `View: ${currentElementData.key}`;
        if (decryptBtn) decryptBtn.textContent = isDownload ? 'Decrypt & Download' : 'Decrypt';
        if (decryptedPasswordInputWrapper) decryptedPasswordInputWrapper.classList.add('hidden');
        if (decryptedPasswordTextareaWrapper) decryptedPasswordTextareaWrapper.classList.add('hidden');
        if (viewMasterKeyInput) {
            viewMasterKeyInput.value = '';
            viewMasterKeyInput.classList.remove('hidden');
        }
        if (decryptBtn) decryptBtn.classList.remove('hidden');
        if (countdownDisplay) countdownDisplay.classList.add('hidden');
        if (viewModal) {
            viewModal.dataset.action = action;
            viewModal.classList.remove('hidden');
            viewMasterKeyInput.focus();
        }
    };

    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            setupActionModal(button, 'view');
        });
    });

    document.querySelectorAll('.download-button').forEach(button => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            setupActionModal(button, 'download');
        });
    });

    decryptBtn?.addEventListener('click', async () => {
        const masterKey = viewMasterKeyInput?.value || '';
        if (!masterKey) return alert('Please enter your master key.');

        const action = viewModal.dataset.action;
        const originalBtnText = decryptBtn.innerHTML;
        decryptBtn.disabled = true;
        decryptBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            if (action === 'download') {
                const response = await fetch(`/myaccount/elements/download/${currentElementData.uuid}`);
                if (!response.ok) throw new Error(`Download failed: ${response.statusText}`);

                const iv = response.headers.get('X-File-IV');
                const salt = response.headers.get('X-File-Salt');
                const hmac = response.headers.get('X-File-Hmac');
                const iterations = parseInt(response.headers.get('X-File-Iterations'), 10);
                const contentDisposition = response.headers.get('Content-Disposition');
                const fileName = contentDisposition?.match(/filename="?(.+)"?/i)?.[1] || 'decrypted-file';
                const fileType = response.headers.get('Content-Type');

                if (!iv || !salt || !hmac || !iterations) throw new Error('Encryption metadata missing.');

                const encryptedData = await response.arrayBuffer();
                const decryptedData = await decryptFile(encryptedData, masterKey, iv, salt, hmac, iterations);
                downloadDecryptedFile(decryptedData, fileName, fileType);
                closeModal();

            } else { // 'view' action
                const response = await fetch(`/myaccount/elements/get/${currentElementData.uuid}`);
                if (!response.ok) throw new Error(`Fetch failed: ${response.statusText}`);
                const data = await response.json();

                if (!validateBase64(data.password) || !validateBase64(data.iv) || !validateBase64(data.salt) || !validateBase64(data.hmac)) {
                    throw new Error('Invalid encrypted data received.');
                }

                const decrypted = await decryptData(data.password, masterKey, data.iv, data.salt, data.hmac, data.iterations);
                secureDecryptedData = new SecureString(decrypted);

                if (currentElementData.type == '1') { // Password
                    decryptedPasswordInput.value = decrypted;
                    decryptedPasswordInputWrapper.classList.remove('hidden');
                } else if (currentElementData.type == '2') { // Text
                    decryptedPasswordTextarea.value = decrypted;
                    decryptedPasswordTextareaWrapper.classList.remove('hidden');
                }

                viewMasterKeyInput.classList.add('hidden');
                decryptBtn.classList.add('hidden');
                startCountdown(30);
            }
        } catch (error) {
            console.error('Process error:', error);
            alert(error.message || 'An error occurred.');
            closeModal();
        } finally {
            decryptBtn.disabled = false;
            decryptBtn.innerHTML = originalBtnText;
        }
    });

    const copyToClipboard = async (textProvider, button) => {
        try {
            let textToCopy = textProvider();
            if (textToCopy) {
                await navigator.clipboard.writeText(textToCopy);
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => { button.innerHTML = originalHtml; }, 2000);
            }
        } catch (err) {
            console.error('Failed to copy text:', err);
            alert('Failed to copy text.');
        }
    };

    copyPasswordBtn?.addEventListener('click', () => copyToClipboard(() => secureDecryptedData?.getValue() || decryptedPasswordInput.value, copyPasswordBtn));
    copyPasswordTextareaBtn?.addEventListener('click', () => copyToClipboard(() => secureDecryptedData?.getValue() || decryptedPasswordTextarea.value, copyPasswordTextareaBtn));

    // --- Folder Navigation ---
    document.querySelectorAll('tbody tr[data-href], .folder-card[data-href]').forEach(item => {
        item.addEventListener('click', function (event) {
            if (event.target.closest('.action-buttons-cell, .action-buttons-card')) return;
            const url = this.dataset.href;
            if (url) window.location.href = url;
        });
    });

    // --- Dynamic Form Fields ---
    const keyLabel = document.getElementById('keyLabel');
    const contentFieldWrapper = document.getElementById('contentFieldWrapper');
    const passwordPlainInput = document.getElementById('passwordPlain');
    const passwordPlainTextarea = document.getElementById('passwordPlainTextarea');
    const fileFormGroup = document.getElementById('fileFormGroup');

    const updateFormFields = () => {
        if (!elementTypeSelect) return;
        const selectedValue = elementTypeSelect.value;

        [fileFormGroup, contentFieldWrapper, passwordPlainInput, passwordPlainTextarea].forEach(el => el?.classList.add('hidden'));
        [passwordPlainInput, passwordPlainTextarea, fileInput].forEach(el => el?.removeAttribute('required'));

        if (selectedValue === '1') { // Password
            if (keyLabel) keyLabel.textContent = 'Element name (key):';
            contentFieldWrapper?.classList.remove('hidden');
            passwordPlainInput?.classList.remove('hidden');
            passwordPlainInput?.setAttribute('required', 'required');
        } else if (selectedValue === '2') { // Text
            if (keyLabel) keyLabel.textContent = 'Element name (key):';
            contentFieldWrapper?.classList.remove('hidden');
            passwordPlainTextarea?.classList.remove('hidden');
            passwordPlainTextarea?.setAttribute('required', 'required');
        } else if (selectedValue === '3') { // File
            if (keyLabel) keyLabel.textContent = 'File name (key):';
            fileFormGroup?.classList.remove('hidden');
            fileInput?.setAttribute('required', 'required');
        } else if (selectedValue === '4') { // Folder
            if (keyLabel) keyLabel.textContent = 'Folder name (key):';
        }
    };

    fileInput?.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
            fileNameDisplay.classList.remove("text-gray-400");
            fileNameDisplay.classList.add("text-gray-800");
        } else {
            fileNameDisplay.textContent = "No file chosen";
            fileNameDisplay.classList.remove("text-gray-800");
            fileNameDisplay.classList.add("text-gray-400");
        }
    });

    elementTypeSelect?.addEventListener('change', updateFormFields);
    updateFormFields(); // Initial call to set form state
});
