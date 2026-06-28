# Project Structure

Standard Laravel 12 directory layout with Filament admin panel.

```
├── app/
│   ├── Console/Commands/       # Artisan commands
│   ├── Filament/               # Admin panel (Filament v3)
│   │   ├── Pages/              # Custom admin pages
│   │   ├── Resources/          # CRUD resources (one per model)
│   │   └── Widgets/            # Dashboard widgets
│   ├── Helpers/                # Utility classes (e.g. UnsplashHelper)
│   ├── Http/Controllers/       # Page controllers (public site)
│   ├── Livewire/               # Livewire components (ContactForm, LeadCaptureModal)
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policies (one per model)
│   ├── Providers/              # Service providers
│   └── helpers.php             # Global helper functions (autoloaded)
├── bootstrap/                  # App bootstrapping
├── config/                     # Laravel config files
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── public/                     # Web root (index.php, compiled assets)
├── resources/
│   ├── css/app.css             # Tailwind CSS entry point
│   ├── js/app.js               # JS entry point (Alpine.js)
│   └── views/
│       ├── components/         # Reusable Blade components (navbar, footer, cards, etc.)
│       ├── errors/             # Error pages
│       ├── filament/           # Filament view overrides
│       ├── html/               # Static HTML templates
│       ├── layouts/            # Page layouts
│       ├── livewire/           # Livewire component views
│       └── pages/              # Full page templates (home, about, blog, etc.)
├── routes/
│   ├── web.php                 # All public + admin routes
│   └── console.php             # Console route definitions
├── storage/                    # Logs, cache, uploaded files
├── tests/                      # PHPUnit tests
├── .ddev/                      # DDEV local development config
├── .github/workflows/          # GitHub Actions CI/CD
└── deploy.sh                   # cPanel deployment helper script
```

## Conventions

- **Controllers**: `PageController` handles all public-facing pages. Admin routes are handled by Filament automatically.
- **Filament Resources**: Each model with CRUD has a corresponding resource in `app/Filament/Resources/`. Resource directories contain `Pages/` for List, Create, Edit, View pages.
- **Blade Components**: Reusable UI pieces live in `resources/views/components/` and are used as `<x-component-name>`.
- **Routing**: Public pages use named routes. Blog article detail uses a catch-all `/{slug}` route (must remain last). Admin panel is at `/gp-strategix`.
- **Models**: All models are in `app/Models/`. Each model that appears in the admin panel has a corresponding Policy in `app/Policies/`.
- **Livewire**: Interactive frontend components (forms, modals) use Livewire classes in `app/Livewire/` with views in `resources/views/livewire/`.
