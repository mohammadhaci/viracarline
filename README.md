# Vira Car Lines AG — Platform

Complete rebuild of viracarsrent.ch: a multilingual public website (DE/FR/EN)
plus a unified back-office split into 5 role-based panels (CMS + ERP).
See `plan.md` for the full plan and `IMPLEMENTATION.md` for the current phase.

**Stack:** Laravel 12 · PHP 8.3+ · Filament v4 · MySQL 8 · Pest · Hostinger shared hosting

## Panels

| Panel    | Path        | Role       | Who                     |
|----------|-------------|------------|-------------------------|
| admin    | `/admin`    | admin      | Site admin (full CMS)   |
| manage   | `/manage`   | gm         | General Manager / CEO   |
| workshop | `/workshop` | mechanic   | Workshop                |
| partner  | `/partner`  | partner    | Investors               |
| finance  | `/finance`  | accountant | Accounting              |

Panel access is enforced in `User::canAccessPanel()` — each role can log into
exactly one panel; everyone else gets 403. Inactive users (`is_active = false`)
are blocked everywhere. Covered by `tests/Feature/PanelAccessTest.php`.

## Local setup (Laragon / Windows)

```bash
git clone <repo-url> vira-platform
cd vira-platform
composer install
copy .env.example .env          # cp on macOS/Linux
php artisan key:generate
```

Configure the database in `.env` (Laragon MySQL 8 default):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vira_platform
DB_USERNAME=root
DB_PASSWORD=

DEMO_PASSWORD=choose-a-local-password
```

Then:

```bash
php artisan migrate --seed
php artisan serve
```

`DEMO_PASSWORD` must be set for the demo users to be seeded (local/staging
only — never hardcoded, never seeded in production).

### Demo users

| Email               | Role       | Panel       |
|---------------------|------------|-------------|
| admin@vira.test     | admin      | `/admin`    |
| gm@vira.test        | gm         | `/manage`   |
| mechanic@vira.test  | mechanic   | `/workshop` |
| partner@vira.test   | partner    | `/partner`  |
| finance@vira.test   | accountant | `/finance`  |

## Queue & scheduler (shared-hosting pattern)

No long-lived worker. A single cron entry runs the Laravel scheduler, which
drains the database queue every minute:

```
* * * * * php /home/USER/vira-platform/artisan schedule:run >> /dev/null 2>&1
```

Locally you can simulate it with `php artisan schedule:work` or just
`php artisan queue:work`.

## Tests & quality

```bash
vendor/bin/pest          # test suite (panel isolation, etc.)
vendor/bin/pint --test   # code style
composer audit           # dependency vulnerabilities
```

CI (GitHub Actions) runs Pint, Pest against MySQL 8, `composer audit`, and
Semgrep (`p/php` + `p/security-audit`) on every push. All jobs must pass.
Commit convention: [Conventional Commits](https://www.conventionalcommits.org).

## Deployment

Hostinger shared hosting — see `docs/DEPLOYMENT.md`.
