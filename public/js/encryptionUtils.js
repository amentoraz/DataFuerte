// public/js/encryptionUtils.js

/**
 * Cleans sensitive strings from memory securely
 * Overwrites memory before releasing the reference
 * @param {string|HTMLInputElement|HTMLTextAreaElement} target - String or DOM element to clean
 * 
 * However, this is not a perfect solution, as the memory is not actually wiped from the system.
 * It is only overwritten with random data, and the original data is still in the system.
 * This is a good solution for most cases, but it is not a perfect solution.
 *  
 * 
 */
export function secureWipe(target) {
    try {
        if (typeof target === 'string') {
            // For direct strings, we can't overwrite due to immutability
            // But we can force garbage collection
            target = null;
        } else if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
            // For DOM elements, overwrite with random data before cleaning
            if (target.value) {
                const originalLength = target.value.length;
                // Overwrite with random characters
                target.value = Array.from(crypto.getRandomValues(new Uint8Array(originalLength)))
                    .map(() => String.fromCharCode(Math.floor(Math.random() * 94) + 33))
                    .join('');
                
                // Repeat overwriting several times
                for (let i = 0; i < 3; i++) {
                    target.value = Array.from(crypto.getRandomValues(new Uint8Array(originalLength)))
                        .map(() => String.fromCharCode(Math.floor(Math.random() * 94) + 33))
                        .join('');
                }
                
                // Finally clean
                target.value = '';
            }
        }
        
        // Force garbage collection if available
        if (window.gc) {
            window.gc();
        }
        
        // Force memory collection in an alternative way
        if (window.opera && window.opera.collect) {
            window.opera.collect();
        }
        
    } catch (error) {
        console.error('Error in secure wipe:', error);
        // Failsafe: at least clean the value
        if (target && typeof target.value !== 'undefined') {
            target.value = '';
        }
    }
}

/**
 * Cleans multiple elements securely
 * @param {...(string|HTMLElement)} targets - Elements to clean
 */
export function secureWipeMultiple(...targets) {
    targets.forEach(target => secureWipe(target));
}

/**
 * Creates a secure container for sensitive strings
 * Allows limited operations and self-destruction
 */
export class SecureString {
    constructor(value, autoDestroyMs = 30000) {
        this._data = value;
        this._destroyed = false;
        
        // Self-destruction after the specified time
        if (autoDestroyMs > 0) {
            this._destroyTimer = setTimeout(() => {
                this.destroy();
            }, autoDestroyMs);
        }
    }
    
    getValue() {
        if (this._destroyed) {
            throw new Error('SecureString has been destroyed');
        }
        return this._data;
    }
    
    destroy() {
        if (!this._destroyed) {
            // Overwrite data
            if (this._data) {
                const length = this._data.length;
                for (let i = 0; i < 3; i++) {
                    this._data = Array.from(crypto.getRandomValues(new Uint8Array(length)))
                        .map(() => String.fromCharCode(Math.floor(Math.random() * 94) + 33))
                        .join('');
                }
            }
            
            this._data = null;
            this._destroyed = true;
            
            if (this._destroyTimer) {
                clearTimeout(this._destroyTimer);
                this._destroyTimer = null;
            }
            
            // Forzar garbage collection
            if (window.gc) window.gc();
        }
    }
    
    // Method to extend the life if necessary
    extendLife(additionalMs = 10000) {
        if (this._destroyed) return false;
        
        if (this._destroyTimer) {
            clearTimeout(this._destroyTimer);
        }
        
        this._destroyTimer = setTimeout(() => {
            this.destroy();
        }, additionalMs);
        
        return true;
    }
}

/**
 * Encrypts plaintext data using a master key, a randomly generated salt, and IV.
 *
 * @param {string} plaintext - The data to encrypt.
 * @param {string} passphrase - The master key used for encryption.
 * @returns {Promise<{encryptedData: string, iv: string, salt: string}>} An object containing the base64 encoded encrypted data, IV, and salt.
 * @throws {Error} If encryption fails.
 */
export async function encryptData(plaintext, passphrase, iterationAmount) {
    let securePassphrase = null;
    
    try {
        if (!passphrase || !plaintext) {
            throw new Error("Plaintext and passphrase are required for encryption.");
        }

        // Create secure container for the passphrase
        securePassphrase = new SecureString(passphrase, 10000); // 10 segundos max

        const salt = crypto.getRandomValues(new Uint8Array(16));
        const iv = crypto.getRandomValues(new Uint8Array(12));

        const keyMaterial = await crypto.subtle.importKey(
            "raw",
            new TextEncoder().encode(securePassphrase.getValue()),
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

    } finally {
        // Clean the secure passphrase
        if (securePassphrase) {
            securePassphrase.destroy();
        }
    }
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
    let secureMasterKey = null;
    
    try {
        if (!ciphertextB64 || !masterKey || !ivB64 || !saltB64 || !hmacB64) {
            throw new Error("All decryption parameters are required.");
        }

        // Create secure container for the master key
        secureMasterKey = new SecureString(masterKey, 5000); // 5 seconds max

        const encoder = new TextEncoder();
        const ciphertext = Uint8Array.from(atob(ciphertextB64), c => c.charCodeAt(0));
        const iv = Uint8Array.from(atob(ivB64), c => c.charCodeAt(0));
        const salt = Uint8Array.from(atob(saltB64), c => c.charCodeAt(0));

        // hmac received
        const hmacReceived = Uint8Array.from(atob(hmacB64), c => c.charCodeAt(0));

        const keyMaterial = await crypto.subtle.importKey(
            "raw",
            encoder.encode(secureMasterKey.getValue()),
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

    } finally {
        // Clean the secure master key
        if (secureMasterKey) {
            secureMasterKey.destroy();
        }
    }
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