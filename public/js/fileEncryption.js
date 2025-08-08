/**
 * Encrypts a file using AES-GCM with a key derived from a master key using PBKDF2.
 * 
 * @param {File} file The file to encrypt.
 * @param {string} masterKey The master key for encryption.
 * @param {number} iterations The number of PBKDF2 iterations.
 * @returns {Promise<Object>} An object containing the encrypted file, IV, salt, HMAC, and file metadata.
 */
export async function encryptFile(file, masterKey, iterations) {
    const salt = window.crypto.getRandomValues(new Uint8Array(16));
    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const keyMaterial = await window.crypto.subtle.importKey(
        'raw',
        new TextEncoder().encode(masterKey),
        { name: 'PBKDF2' },
        false,
        ['deriveKey']
    );
    const key = await window.crypto.subtle.deriveKey(
        {
            name: 'PBKDF2',
            salt: salt,
            iterations: iterations,
            hash: 'SHA-256',
        },
        keyMaterial,
        { name: 'AES-GCM', length: 256 },
        true,
        ['encrypt', 'decrypt']
    );

    const fileBuffer = await file.arrayBuffer();
    const encryptedFile = await window.crypto.subtle.encrypt(
        {
            name: 'AES-GCM',
            iv: iv,
        },
        key,
        fileBuffer
    );

    // Generate HMAC
    const hmacKey = await window.crypto.subtle.importKey(
        'raw',
        new TextEncoder().encode(masterKey + "_hmac"), // Use a different context for HMAC key
        { name: 'HMAC', hash: { name: 'SHA-256' } },
        false,
        ['sign']
    );
    const hmac = await window.crypto.subtle.sign(
        'HMAC',
        hmacKey,
        encryptedFile
    );

    return {
        encryptedFile: new Blob([encryptedFile]),
        iv: btoa(String.fromCharCode.apply(null, iv)),
        salt: btoa(String.fromCharCode.apply(null, salt)),
        hmac: btoa(String.fromCharCode.apply(null, new Uint8Array(hmac))),
        fileName: file.name,
        fileSize: file.size,
        fileType: file.type,
    };
}

/**
 * Decrypts a file using AES-GCM and verifies its integrity with HMAC.
 * 
 * @param {ArrayBuffer} encryptedData The encrypted file data.
 * @param {string} masterKey The master key for decryption.
 * @param {string} ivBase64 The base64-encoded IV.
 * @param {string} saltBase64 The base64-encoded salt.
 * @param {string} hmacBase64 The base64-encoded HMAC.
 * @param {number} iterations The number of PBKDF2 iterations.
 * @returns {Promise<ArrayBuffer>} The decrypted file data.
 */
export async function decryptFile(encryptedData, masterKey, ivBase64, saltBase64, hmacBase64, iterations) {
    const iv = new Uint8Array(atob(ivBase64).split('').map(c => c.charCodeAt(0)));
    const salt = new Uint8Array(atob(saltBase64).split('').map(c => c.charCodeAt(0)));
    const hmacReceived = new Uint8Array(atob(hmacBase64).split('').map(c => c.charCodeAt(0)));

    // Verify HMAC
    const hmacKey = await window.crypto.subtle.importKey(
        'raw',
        new TextEncoder().encode(masterKey + "_hmac"),
        { name: 'HMAC', hash: { name: 'SHA-256' } },
        false,
        ['verify']
    );
    const isValid = await window.crypto.subtle.verify(
        'HMAC',
        hmacKey,
        hmacReceived,
        encryptedData
    );

    if (!isValid) {
        throw new Error('HMAC verification failed. The file may have been tampered with.');
    }

    const keyMaterial = await window.crypto.subtle.importKey(
        'raw',
        new TextEncoder().encode(masterKey),
        { name: 'PBKDF2' },
        false,
        ['deriveKey']
    );
    const key = await window.crypto.subtle.deriveKey(
        {
            name: 'PBKDF2',
            salt: salt,
            iterations: iterations,
            hash: 'SHA-256',
        },
        keyMaterial,
        { name: 'AES-GCM', length: 256 },
        true,
        ['decrypt']
    );

    try {
        const decryptedData = await window.crypto.subtle.decrypt(
            {
                name: 'AES-GCM',
                iv: iv,
            },
            key,
            encryptedData
        );
        return decryptedData;
    } catch (error) {
        throw new Error('Decryption failed. The master key may be incorrect.');
    }
}

/**
 * Triggers a browser download for a decrypted file.
 * 
 * @param {ArrayBuffer} decryptedData The decrypted file data.
 * @param {string} fileName The name of the file to be saved.
 * @param {string} fileType The MIME type of the file.
 */
export function downloadDecryptedFile(decryptedData, fileName, fileType) {
    const blob = new Blob([decryptedData], { type: fileType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
