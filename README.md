# DataFuerte

A secure system for managing personal keys via a web server.

Please note this won't work without HTTPS, since it uses WebCrypto.

---
⚠️ **Basic functionality still on development** ⚠️
---

⚙️ TODO before this system can be used

Basically data organization should be stable so that nothing is lost with updates

* Folders to organize data (passwords, texts and files).
* Extend the logic from passwords (where the main development is now taking place) to texts and files. Otherwise unify everything into one screen, filesystem-like, with filters.
* Basic configuration screen with parameters. Probably the user should be forced to go here after installation if settings such as iterations are configurable. Some of these may become constants after instalation unless a hard reset is done.
* Final security assessment before the database and algorithms are set in stone.

⚙️ TODO further

* Automatic installation script.
* Recommendations on Master Key creation (length, etc). Since it MUST be written everytime content is decrypted, now by design you can use different Master Keys. Probably this is a good idea.
* If several algorithms are considered, they might be chosen in the general configuration and/or for each content.
* File tags for the three kinds of content, to allow further organization.
* Hard reset mechanism.
* Search filters for passwords/texts/files.
* CSP headers and other security mechanisms.
* Unify all the screens into one and turn it into a virtual "filesystem". This may happen in the previous development phase.
* Fully APIfy the system to allow external clients for those who don't want to use the current frontend (?)



## 🛠 How to install

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