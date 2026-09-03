# ABCN API

Laravel API for the ABCN public website, administration panel and future product catalog.

## Requirements

- PHP 8.2 or newer
- Composer 2
- SQLite for local development, MySQL or PostgreSQL for production

The approved Windows environment (`PHP 8.2.12`) is compatible with this Laravel 12 project.

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve
```

Before seeding, set the first administrator in `.env`:

```env
ADMIN_NAME="ABCN Administrator"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="use-a-long-unique-password"
FRONTEND_URL=http://localhost:5173
```

Never commit the real `.env` file or administrator password.

## Main endpoints

- `GET /api/v1/health`
- `GET /api/v1/pages/{slug}`
- `GET /api/v1/product-categories`
- `GET /api/v1/products`
- `POST /api/v1/contact-requests`
- `POST /api/v1/admin/login`
- `GET /api/v1/admin/dashboard`
- Admin CRUD endpoints for pages, categories and products

## Validation

```bash
php artisan test
./vendor/bin/pint --test
```
