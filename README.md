# ABCN API

Laravel API for the ABCN public website, administration panel and future product catalog.

## Requirements

- PHP 8.2 or newer
- Composer 2
- MySQL 8.0 or newer

The approved Windows environment (`PHP 8.2.12`) is compatible with this Laravel 12 project.

## Local setup

Create an empty `abcn` database in phpMyAdmin, or run:

```sql
CREATE DATABASE abcn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then configure and start the API:

```bat
composer install
copy .env.example .env
php artisan key:generate
```

Set the MySQL connection and first administrator in `.env`:

```env
ADMIN_NAME="ABCN Administrator"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="use-a-long-unique-password"
FRONTEND_URL=http://localhost:5173
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abcn
DB_USERNAME=root
DB_PASSWORD=
```

Never commit the real `.env` file or administrator password.

Run the migrations, seed the administrator and start the API:

```bat
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Main endpoints

- `GET /api/v1/health`
- `GET /api/v1/pages/{slug}`
- `GET /api/v1/product-categories`
- `GET /api/v1/products`
- `POST /api/v1/contact-requests`
- `POST /api/v1/admin/login`
- `GET /api/v1/admin/dashboard`
- Admin CRUD endpoints for pages, categories and products
- Admin media endpoints for JPG, PNG, WebP and PDF uploads

## Validation

```bash
php artisan test
./vendor/bin/pint --test
```
