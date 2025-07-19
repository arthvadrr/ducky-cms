# ducky-cms

A simple, flat, PHP-based CMS with SQLite.

## Get Started

1. Clone the repo.
2. Make sure `db/` exists and is writable.
3. Run the app on any PHP server (e.g. `php -S localhost:8000`).
4. Visit `/` in your browser and follow the setup steps:
   - Create database
   - Set site URL
   - Create admin user
5. Log in and start building.

## Tech

- PHP 8.2+
- SQLite
- Zero dependencies

## Database

One file: `db/ducky.sqlite`  
Schema is in `db/schema.php`

## Structure

- `setup/` – initial install
- `auth/` – login/logout
- `dashboard/` – admin UI
- `includes/` – shared functions