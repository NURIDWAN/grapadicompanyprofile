# Tech Stack

## Backend

- **Framework**: Laravel 12 (PHP 8.2+)
- **Admin Panel**: Filament v3 (accessible at `/gp-strategix`)
- **Database**: MariaDB 10.11 (production uses MySQL on cPanel)
- **Auth/Permissions**: Filament Shield + Spatie Laravel Permission
- **Image Optimization**: Spatie Laravel Image Optimizer
- **Livewire**: Used for interactive components (ContactForm, LeadCaptureModal)

## Frontend

- **Build Tool**: Vite 7 with `laravel-vite-plugin`
- **CSS**: Tailwind CSS v4 (via `@tailwindcss/vite` plugin)
- **JS**: Alpine.js with `@alpinejs/intersect` for scroll animations
- **Templating**: Blade (component-based architecture)

## Local Development

- **Environment**: DDEV (PHP 8.3, nginx-fpm, MariaDB 10.11)
- **Package Managers**: Composer 2 (PHP), npm (JS)

## Deployment

- **Hosting**: cPanel (shared hosting via FTP)
- **CI/CD**: GitHub Actions deploys on push to `main`/`master`
- **Process**: Build assets locally in CI → FTP upload → manual artisan commands on server

## Common Commands

```bash
# Start all dev services (server, queue, logs, vite)
composer dev

# Install dependencies
composer install
npm install

# Build frontend assets
npm run build

# Run Vite dev server only
npm run dev

# Run tests
composer test
# or directly:
php artisan test

# Lint PHP code
./vendor/bin/pint

# Database
php artisan migrate
php artisan db:seed

# Cache management (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Filament
php artisan filament:upgrade
php artisan shield:generate --all
```

## Key Configuration Files

- `vite.config.js` — Vite + Tailwind + Laravel plugin
- `.ddev/config.yaml` — Local dev environment
- `composer.json` — PHP dependencies and scripts
- `package.json` — JS dependencies and build scripts
- `.env` / `.env.production` — Environment config
