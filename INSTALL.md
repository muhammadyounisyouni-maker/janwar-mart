# Janwar Mart (جانور مارٹ) — Laravel version: install & run

Needs: **PHP 8.3+** (extensions: pdo_mysql, mbstring, openssl, fileinfo, gd or imagick not required), **Composer 2**, **MySQL 8 / MariaDB 10.6+**. XAMPP (Windows) works: it already has PHP + MySQL.

## 1. Install (once)
```bash
cd janwar-mart-laravel
composer install
copy .env.example .env          # Windows   (Linux/Mac: cp .env.example .env)
php artisan key:generate
```
Create an empty database in phpMyAdmin (XAMPP) named `janwar_mart` (utf8mb4_unicode_ci).

## 2. Configure `.env`
| Key | Meaning |
|---|---|
| `APP_URL` | `http://localhost:8000` locally, your `https://` domain when live |
| `DB_DATABASE / DB_USERNAME / DB_PASSWORD` | your MySQL details (XAMPP: user `root`, empty password) |
| `ADMIN_NAME / ADMIN_PHONE / ADMIN_EMAIL / ADMIN_PASSWORD` | the **first admin**. Password must be 12+ chars with upper, lower, digit and symbol, otherwise the seeder refuses |
| `SMS_PROVIDER` | `console` (OTP code is written to `storage/logs/laravel.log`), `twilio` or `webhook` |
| `TWILIO_*`, `SMS_WEBHOOK_*` | only for the provider you chose |
| `CONTACT_NUMBER` | admin phone for the Contact page, digits only, e.g. `923001234567` |

## 3. Create tables, first data, image link
```bash
php artisan migrate
php artisan db:seed            # 4 categories (cow, buffalo, goat, sheep) + first admin from .env
php artisan storage:link
```

## 4. Run on localhost
```bash
php artisan serve
```
Open http://localhost:8000 . Admin login: the ADMIN phone + password, then you are sent to `/admin`.
Phone OTP in local mode: press "Send code", then open `storage/logs/laravel.log` and copy the 6-digit code.

## 5. Move your OLD data (from the Python version) — optional, safe
The old project is never touched. Copy its `data/` folder next to this one, then:
```bash
php artisan janwar:import-legacy "C:\path\to\data\janwar.db" --uploads="C:\path\to\data"
```
* keeps all ids (JM-1101, RQ-2001 ... stay the same), copies photos and private doctor documents
* never deletes or overwrites rows that already exist
* old users log in with their **old password** (it is upgraded to a new secure hash on first login)
* the old shared admin password is **not** imported — you now log in with the admin account from `.env`
* the file name of the old database may differ; look in the Python `data/` folder.

## 6. Run the tests
```bash
php artisan test
```
(uses an in-memory SQLite database, so it never touches your MySQL data; needs the `pdo_sqlite` PHP extension)

## 7. Going live (checklist)
* `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL` with **https://**
* point the web server document root to `public/`
* strong `ADMIN_PASSWORD`; `SMS_PROVIDER=twilio` or `webhook` (not `console`)
* `php artisan config:cache && php artisan route:cache && php artisan view:cache`
* the HSTS header is sent automatically over HTTPS
* back up the database and `storage/app`

## Project map
`routes/web.php`, `routes/api.php` · `app/Http/Controllers` (+`Admin/`) · `app/Http/Middleware` (`EnsureAdmin`, `EnsureNotBlocked`, `SetLocale`, `SecurityHeaders`) · `app/Http/Requests` · `app/Http/Resources` · `app/Services` (OTP, SMS, cart, orders, images) · `app/Models` · `database/migrations` · `database/seeders` · `resources/views` (Blade) · `lang/ur.json` (Urdu texts) · `public/style.css` (original design) · `tests/Feature`.
Design: the public site keeps the original theme (green/gold, Noto Nastaliq Urdu, Inter, Playfair Display, dark mode, RTL/LTR, mobile bottom bar). The **admin panel uses Bootstrap 5** (loaded from a CDN); the public site uses the original stylesheet because Bootstrap's class names (`.card`, `.btn`, `.nav`) would clash with the original design.
