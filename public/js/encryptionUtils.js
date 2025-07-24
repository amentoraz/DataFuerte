// public/js/encryptionUtils.js

/**
 * Encrypts plaintext data using a master key, a randomly generated salt, and IV.
 *
 * @param {string} plaintext - The data to encrypt.
 * @param {string} passphrase - The master key used for encryption.
 * @returns {Promise<{encryptedData: string, iv: string, salt: string}>} An object containing the base64 encoded encrypted data, IV, and salt.
 * @throws {Error} If encryption fails.
 */
export async function encryptData(plaintext, passphrase, iterationAmount) {
    if (!passphrase || !plaintext) {
        throw new Error("Plaintext and passphrase are required for encryption.");
    }

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
            iterations: iterationAmount,
            hash: "SHA-256"
        },
        keyMaterial,
        { name: "AES-GCM", length: 256 },
        false,
        ["encrypt"]
    );

    // Derivate the HMAC key
    const hmacKey = await crypto.subtle.deriveKey(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: iterationAmount,
            hash: "SHA-256"
        },
        keyMaterial,
        { name: "HMAC", hash: "SHA-256" },
        false,
        ["sign"]
    );

    // Encrypt the plaintext
    const encrypted = await crypto.subtle.encrypt(
        { name: "AES-GCM", iv: iv },
        key,
        new TextEncoder().encode(plaintext)
    );

    // Encode to base64
    const toBase64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf)));

    // Generate HMAC from encrypted data, IV and salt
    const dataToSign = new Uint8Array([...new Uint8Array(encrypted), ...iv, ...salt]);
    const hmacSignature = await crypto.subtle.sign("HMAC", hmacKey, dataToSign);

    return {
        encryptedData: toBase64(encrypted),
        iv: toBase64(iv),
        salt: toBase64(salt),
        hmac: toBase64(hmacSignature)
    };
}

/**
 * Decrypts base64 encoded ciphertext using a master key, IV, and salt.
 *
 * @param {string} ciphertextB64 - The base64 encoded encrypted data.
 * @param {string} masterKey - The master key used for decryption.
 * @param {string} ivB64 - The base64 encoded Initialization Vector.
 * @param {string} saltB64 - The base64 encoded salt.
 * @returns {Promise<string>} The decrypted plaintext.
 * @throws {Error} If decryption fails (e.g., incorrect key, corrupted data).
 */
export async function decryptData(ciphertextB64, masterKey, ivB64, saltB64, hmacB64, iterationAmount) {
    if (!ciphertextB64 || !masterKey || !ivB64 || !saltB64 || !hmacB64) {
        throw new Error("All decryption parameters are required.");
    }

    const encoder = new TextEncoder();
    const ciphertext = Uint8Array.from(atob(ciphertextB64), c => c.charCodeAt(0));
    const iv = Uint8Array.from(atob(ivB64), c => c.charCodeAt(0));
    const salt = Uint8Array.from(atob(saltB64), c => c.charCodeAt(0));

    // hmac received
    const hmacReceived = Uint8Array.from(atob(hmacB64), c => c.charCodeAt(0));


    const keyMaterial = await crypto.subtle.importKey(
        "raw",
        encoder.encode(masterKey),
        { name: "PBKDF2" },
        false,
        ["deriveKey"]
    );

    // Derivate the AES key
    const key = await crypto.subtle.deriveKey(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: iterationAmount,
            hash: "SHA-256"
        },
        keyMaterial,
        { name: "AES-GCM", length: 256 },
        false,
        ["decrypt"]
    );

    // Derivate the HMAC key
    const hmacKey = await crypto.subtle.deriveKey(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: iterationAmount,
            hash: "SHA-256"
        },
        keyMaterial,
        { name: "HMAC", hash: "SHA-256" },
        false,
        ["verify"]
    );

    // Verify HMAC before decrypting
    const dataToVerify = new Uint8Array([...ciphertext, ...iv, ...salt]);
    const isValid = await crypto.subtle.verify("HMAC", hmacKey, hmacReceived, dataToVerify);

    if (!isValid) {
        throw new Error("HMAC verification failed - data may have been tampered with");
    }


    const decrypted = await crypto.subtle.decrypt(
        { name: "AES-GCM", iv: iv },
        key,
        ciphertext
    );
    return new TextDecoder().decode(decrypted);
}

/**
 * Validates if a string is a valid base64 encoded string.
 * @param {string} str - The string to validate.
 * @returns {boolean} True if the string is valid base64, false otherwise.
 */
export function validateBase64(str) {
    try {
        return btoa(atob(str)) === str;
    } catch (e) {
        return false;
    }
}