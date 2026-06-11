# IMPLEMENTATION.md — Phase 0: Foundation

**Project:** Vira Car Lines AG Platform (see plan.md)
**Phase goal:** A deployable Laravel 12 skeleton with 5 isolated Filament panels, role-based access, demo users, tests proving panel isolation, and CI.
**Definition of done:** Each of the 5 demo users logs into exactly ONE panel and receives 403 on the other four. CI is green (Pint + Pest + Semgrep + composer audit).

**Target runtime:** PHP 8.3, MySQL 8, Hostinger SHARED hosting (no Redis, cron-based queue).

-----

## Step 1 — Project Init

```bash
composer create-project laravel/laravel vira-platform
cd vira-platform
git init && git add . && git commit -m "chore: laravel 12 skeleton"
```

`config/app.php` / `.env`:

```env
APP_NAME="Vira Car Lines"
APP_TIMEZONE=Europe/Zurich
APP_LOCALE=de
APP_FALLBACK_LOCALE=de
APP_FAKER_LOCALE=de_CH

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Supported locales config — create `config/locales.php`:

```php
return ['supported' => ['de', 'fr', 'en'], 'default' => 'de'];
```

```bash
php artisan session:table && php artisan queue:table && php artisan cache:table
```

## Step 2 — Packages

```bash
composer require filament/filament:"^4.0" \
  spatie/laravel-permission \
  spatie/laravel-activitylog \
  spatie/laravel-medialibrary \
  spatie/laravel-translatable \
  spatie/laravel-backup

composer require pestphp/pest pestphp/pest-plugin-laravel laravel/pint --dev
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

Note: medialibrary + translatable are installed now to lock versions; their migrations/usage start in Phase 1–2.

## Step 3 — Roles

`database/seeders/RoleSeeder.php` — create exactly these role names (referenced everywhere; do not rename later):

```php
foreach (['admin', 'gm', 'mechanic', 'partner', 'accountant'] as $role) {
    Role::findOrCreate($role, 'web');
}
```

Permission granularity comes in later phases; Phase 0 gates panels by ROLE only.

## Step 4 — The 5 Filament Panels

```bash
php artisan filament:install --panels        # creates AdminPanelProvider (id: admin, path: /admin)
php artisan make:filament-panel manage       # GM        → /manage
php artisan make:filament-panel workshop     # Mechanic  → /workshop
php artisan make:filament-panel partner      # Partner   → /partner
php artisan make:filament-panel finance      # Accountant→ /finance
```

Each panel provider: `->login()`, `->authGuard('web')`, brand name + logo placeholder, distinct primary color per panel (all derived from the existing Vira palette in Phase 2; use Filament defaults for now):

|Panel id|Path     |Role required|
|--------|---------|-------------|
|admin   |/admin   |admin        |
|manage  |/manage  |gm           |
|workshop|/workshop|mechanic     |
|partner |/partner |partner      |
|finance |/finance |accountant   |

## Step 5 — Panel Access Control (User model)

`app/Models/User.php`:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;

    public const PANEL_ROLE_MAP = [
        'admin'    => 'admin',
        'manage'   => 'gm',
        'workshop' => 'mechanic',
        'partner'  => 'partner',
        'finance'  => 'accountant',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        $role = self::PANEL_ROLE_MAP[$panel->getId()] ?? null;

        return $this->is_active
            && $role !== null
            && $this->hasRole($role);
    }
}
```

Migration: add `is_active` (boolean, default true) and `locale` (string, default ‘de’) to `users`.

Rule: the `admin` role does NOT automatically access other panels in Phase 0. Cross-panel convenience can be decided later; isolation first.

## Step 6 — Demo Users Seeder

`database/seeders/DemoUserSeeder.php` — one user per role:

```
admin@vira.test / gm@vira.test / mechanic@vira.test / partner@vira.test / finance@vira.test
Password (local only): from env DEMO_PASSWORD, never hardcoded.
```

`DatabaseSeeder` runs RoleSeeder then DemoUserSeeder. Guard the demo seeder with `if (app()->environment('local', 'staging'))`.

## Step 7 — Pest Tests (panel isolation)

`tests/Feature/PanelAccessTest.php` — the core Phase 0 deliverable:

```php
$panels = [
    'admin'    => '/admin',
    'gm'       => '/manage',
    'mechanic' => '/workshop',
    'partner'  => '/partner',
    'accountant' => '/finance',
];

// 1. Correct role → 200 on its panel dashboard
// 2. Every other role → 403 on that panel
// 3. Guest → redirect to that panel's /login
// 4. Inactive user (is_active=false) → 403 everywhere
```

Implement as a dataset-driven Pest test covering all 5×5 combinations + guest + inactive. ~27 assertions minimum.

## Step 8 — Scheduler & Queue (shared-hosting pattern)

`routes/console.php`:

```php
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
```

Hostinger cron (single entry, documented in README):

```
* * * * * php /home/USER/vira-platform/artisan schedule:run >> /dev/null 2>&1
```

## Step 9 — CI (GitHub Actions)

`.github/workflows/ci.yml`:

- **lint:** `vendor/bin/pint --test`
- **test:** MySQL 8 service, `php artisan test` (Pest)
- **security:** `composer audit` + Semgrep (`semgrep/semgrep-action` with `p/php` + `p/security-audit` rulesets)

All three jobs required to pass. Commit convention: Conventional Commits.

## Step 10 — Deployment Skeleton (document only, deploy in Phase 10)

Add `docs/DEPLOYMENT.md` stub describing the Hostinger shared layout:

```
/home/USER/vira-platform        ← app root (git deploy)
/home/USER/public_html          ← contents of /public (or symlink if plan allows)
storage/app/private             ← partner docs, NEVER under public_html
```

Plus: PHP 8.3 selection in hPanel, `php artisan storage:link` note, `.env` handling, cron entry from Step 8.

-----

## Acceptance Checklist

- [ ] `php artisan migrate --seed` runs clean on fresh MySQL 8
- [ ] 5 panels reachable, each with its own login page
- [ ] PanelAccessTest: all combinations pass (correct=200, wrong=403, guest=redirect, inactive=403)
- [ ] `pint --test`, `composer audit`, Semgrep: clean
- [ ] CI green on push
- [ ] README documents local setup (Laragon/Windows) + cron pattern
- [ ] Tag `v0.1.0-phase0`, commit `feat: phase 0 foundation — 5-panel skeleton`

## Out of Scope for Phase 0

No business models (vehicles/partners/invoices), no CMS, no public frontend, no translations UI, no media handling. Next: Phase 1 (Core Domain Models) — see plan.md §7.
