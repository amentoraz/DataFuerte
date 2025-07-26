# DataFuerte

A secure system for managing personal keys via a web server.

This system stores encrypted short and long texts remotely, without ever sending the plaintext to the server.

It features:

---

## Client-Side Encryption with Robust Security

DataFuerte prioritizes your privacy and security by implementing **end-to-end encryption** where all sensitive operations occur directly in your browser. This means your plaintext data **never leaves your device** and is never accessible to the server.

Here’s how the encryption process works:

1.  **Key Derivation (PBKDF2):** When you input your data and a passphrase, DataFuerte uses **PBKDF2 (Password-Based Key Derivation Function 2)** with a randomly generated **salt** and a high number of **iterations**. This process derives two strong cryptographic keys from your passphrase: an **AES key** for encryption/decryption and an **HMAC key** for data integrity verification.
2.  **Data Encryption (AES-256 GCM):** Your plaintext is then encrypted using **AES-256 in GCM (Galois/Counter Mode)**. GCM is an authenticated encryption mode that provides both confidentiality and integrity, ensuring that your data is not only scrambled but also protected against tampering. A unique **Initialization Vector (IV)** is generated for each encryption operation, adding an extra layer of security.
3.  **Data Integrity (HMAC-SHA256):** To prevent unauthorized modification of your encrypted data, an **HMAC (Hash-based Message Authentication Code)** is generated using the HMAC key derived earlier. This HMAC is a cryptographic checksum that ensures the integrity and authenticity of the encrypted data, IV, and salt.
4.  **Secure Storage:** Only the **ciphertext, IV, salt, and HMAC** are transmitted to the server for storage. Your original plaintext and passphrase remain exclusively on your client-side.
5.  **Secure Decryption:** When you retrieve your encrypted data, the stored ciphertext, IV, salt, and HMAC are sent back to your browser. Your master passphrase (which is **never stored or sent to the server**) is used locally to re-derive the AES and HMAC keys. The system first **verifies the HMAC** to ensure data integrity before proceeding with decryption. If the HMAC check passes, the AES key and IV are used to decrypt the data back into plaintext.
6.  **Memory Wipe (SecureString & secureWipe):** DataFuerte employs robust techniques to minimize the exposure of sensitive data in memory. The `SecureString` class creates a temporary, self-destructing container for your passphrase and decrypted data. Additionally, the `secureWipe` utility overwrites input fields and string variables with random data before being released, significantly reducing the risk of sensitive information being recovered from memory.

---

## Technical Highlights

* **Laravel Backend:** A robust and secure PHP framework powers the server-side, handling user authentication, data storage (of encrypted blobs), and API interactions.
* **JavaScript Cryptography:** All encryption and decryption logic is implemented in JavaScript on the client-side, leveraging the Web Cryptography API for strong, browser-native cryptographic operations.
* **Folder Organization:** The system supports organizing your encrypted entries into a hierarchical folder structure for better management.
* **Comprehensive Logging:** Detailed logs are maintained for actions like element access and deletion attempts, providing an audit trail for security monitoring.
* **Unauthorized Access Prevention:** The server-side code includes checks to ensure that users can only access or modify elements that belong to them, preventing unauthorized data manipulation.

---


## Getting Started -- 🛠 How to install

**Please note this software won't work without HTTPS, since it uses WebCrypto.**

### 1. Create the database

Login to MySQL:

```bash
mysql -u root -p
```
Then run:

```sql
CREATE DATABASE datafuerte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'user_datafuerte'@'localhost' IDENTIFIED BY 'your_pass';
GRANT ALL PRIVILEGES ON datafuerte.* TO 'user_datafuerte'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```


### 2. Copy .env.example to .env and configure it.

You will need at least:

```ini
APP_NAME=Datafuerte
APP_URL=http://datafuerte.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=datafuerte
DB_USERNAME=your_user
DB_PASSWORD=your_pass

# This will be the data for your admin user
SEED_USER_NAME=administrator
SEED_USER_EMAIL=admin@example.com
SEED_USER_PASSWORD=password

# More stuff you may want to configure according to your domain
APP_URL=http://datafuerte.local
SESSION_DOMAIN=datafuerte.local
```


### 3. Generate your APP KEY

```bash
php artisan key:generate
```


### 4. Install vendor dependencies

From the root directory:

```bash
composer install
```


### 5. Run migrations to prepare the database

From the root directory 

```bash
php artisan migrate
```

### 6. Run seeders to create first admin user

```bash
php artisan db:seed
```


### 7. Configure HTTPS domain

Browsers do not allow WebCrypto in HTTP without SSL.

You may want to create your own self-signed certificate by doing:

```bash
mkdir -p ~/ssl && cd ~/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout datafuerte.local.key -out datafuerte.local.crt \
  -subj "/CN=datafuerte.local"
```

## 7.1. Apache

Then configuring your apache2/sites-available/datafuerte.local.conf file like:

```ini
<VirtualHost *:443>

    ServerName datafuerte.local

    SSLEngine on
    SSLCertificateFile /home/myuser/ssl/datafuerte.local.crt
    SSLCertificateKeyFile /home/myuser/ssl/datafuerte.local.key

        DocumentRoot /var/www/datafuerte/public
        <Directory /var/www/datafuerte/public>
                Options FollowSymLinks
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

    ErrorLog ${APACHE_LOG_DIR}/datafuerte_error.log
    CustomLog ${APACHE_LOG_DIR}/datafuerte_access.log combined

</VirtualHost>
```

Don't forget to 

```ini
a2enmod ssl
```

## 7.2. Nginx

Otherwise, if you're using nginx, you may want to do

```ini
server {
    listen 443 ssl;
    server_name localhost;

    ssl_certificate     /home/myuser/ssl/datafuerte.local.crt
    ssl_certificate_key /home/myuser/ssl/datafuerte.local.key

    root /var/www/datafuerte/public;
    ...
}
```



## License

Under AGPL-3.0 license. Read LICENSE for more details.




