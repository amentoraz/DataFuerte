# DataFuerte

A secure system for managing personal keys via a web server.

---

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

### 6 Run seeders to create first admin user

```bash
php artisan db:seed
```
