# The Wandering Fork — PHP version

This is a standalone PHP copy of the Next.js website. It lives in `php-version/`; the original project files are separate and were not changed for this conversion.

## Hosting

- Upload the contents of this folder into a PHP-enabled Apache document root or subfolder.
- Upload the current folder contents; `README.zip` is an older frontend-only archive and does not include the PHP backend or database setup.
- Keep the included photo folders at the same level as `index.php` (this folder already contains them).
- PHP 8 or newer and Apache `mod_rewrite` are recommended. The included `.htaccess` maps `/about`, `/services`, `/gallery`, `/how`, `/contact`, and `/start-planning` to the PHP page router.
- For cPanel hosting, use a plan that provides PostgreSQL and PHP's `pdo_pgsql` extension. Configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` with the exact connection values shown by your host.

## Database (PostgreSQL)

The site content and admin data live in **PostgreSQL**. The PHP PDO connection is in `config/database.php` and reads these environment variables:

- `DB_HOST` — PostgreSQL host (default `127.0.0.1`)
- `DB_PORT` — port (default `5432`)
- `DB_NAME` — database name (default `wandering-fork-site`)
- `DB_USER` — database user (default `wandering-fork-site`)
- `DB_PASSWORD` — database password (default `1234`)

These defaults are for local development only. On cPanel, create a database and user in the PostgreSQL interface, then set all five `DB_*` values to the exact host-provided values (including any account prefix). Use a unique database password.

Create the database/user (example for PostgreSQL):

```sql
CREATE DATABASE "wandering-fork-site";
CREATE USER "wandering-fork-site" WITH PASSWORD '1234';
GRANT ALL PRIVILEGES ON DATABASE "wandering-fork-site" TO "wandering-fork-site";
```

Then load the schema and seed data:

```bash
psql -U wandering-fork-site -d wandering-fork-site -f database/schema.sql
```

The schema creates:

- `admin_users` — admin login (default: `admin` / `admin@123`; change before deployment)
- `site_content` — key/value content (hero, site info, socials, home photos & quotes, how steps)
- `services` — private chef services shown on the site
- `reviews` — guest reviews shown on the site
- `how_steps` — "How it works" steps
- `gallery_items` — gallery photos shown on the site
- `inquiries` — inquiries submitted from the "Start planning" form

## Admin panel

- Login: `http://localhost:8000/admin/login.php` (username `admin`, password `admin@123`)
- Dashboard: `/admin/index.php`
- Content editor: `/admin/content.php`
- Gallery manager: `/admin/gallery.php`
- Inquiries inbox: `/admin/inquiries.php`
- Logout: `/admin/logout.php`

The admin area uses PHP sessions (`includes/auth.php`) and requires a logged in admin user.

## Public inquiry form

The "Start planning" form posts to `api/submit-inquiry.php`, which stores submissions in PostgreSQL. The admin inbox at `/admin/inquiries.php` lists and manages them. The page does not use the visitor's email application.

## Frontend

The page-specific templates are in `views/`, shared navigation and footer are in `includes/`, and the original photos are included in the web root.
