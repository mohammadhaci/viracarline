# DEPLOYMENT.md — Hostinger Shared Hosting Runbook

Production deployment guide for the Vira Car Lines platform
(Laravel 12, PHP 8.3, MySQL 8, no Redis, no Node on the server).

## Target layout

```
/home/USER/vira-platform        ← app root (git deploy)
/home/USER/public_html          ← contents of /public (or symlink if plan allows)
storage/app/private             ← partner docs, contracts, invoices, receipts — NEVER under public_html
```

The application root lives OUTSIDE `public_html`. Only Laravel's `/public`
directory is web-accessible. All private documents (partner statements,
sale contracts, invoice PDFs, expense receipts, Ankauf photos) are stored
on the `local` disk under `storage/app/private` and served exclusively
through authenticated, signed routes.

## First deployment

1. **PHP version:** select PHP 8.3 in hPanel; enable extensions
   `pdo_mysql`, `intl`, `gd`, `exif`, `fileinfo`.
2. **Clone:** `git clone <repo> /home/USER/vira-platform`.
3. **Dependencies:** `composer install --no-dev --optimize-autoloader`.
4. **Frontend assets:** build locally or in CI (`npm ci && npm run build`)
   and upload `public/build/` — the shared host has no Node.
5. **Webroot:** point `public_html` at `vira-platform/public`
   (symlink if the plan allows, otherwise copy contents and adjust
   `index.php` paths).
6. **Environment:** copy `.env.example` → `.env`, then set:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`
   - `php artisan key:generate`
   - MySQL credentials from hPanel
   - mail settings (SMTP) for lead notifications
   - leave `DEMO_PASSWORD` EMPTY in production (demo users are never seeded outside local/staging)
7. **Database:** `php artisan migrate --force` then `php artisan db:seed --class=RoleSeeder --force`.
   Create the first admin user via tinker and assign the `admin` role.
8. **Storage symlink:** `php artisan storage:link` (public disk only).
9. **Caches:** `php artisan config:cache route:cache view:cache` (rerun after every deploy).
10. **Cron (single entry — scheduler AND queue AND backups):**

    ```
    * * * * * php /home/USER/vira-platform/artisan schedule:run >> /dev/null 2>&1
    ```

    The scheduler runs `queue:work --stop-when-empty --max-time=50` every
    minute plus `backup:clean` (01:30) and `backup:run` (02:00) nightly.

## Each subsequent deploy

```bash
cd /home/USER/vira-platform
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

Upload a fresh `public/build/` whenever frontend assets changed.

## Backups & restore drill

- `spatie/laravel-backup` writes nightly DB + storage archives (config in
  `config/backup.php`); destination defaults to the `local` disk — for real
  redundancy add an off-site disk (S3-compatible) in production `.env`.
- **Restore drill (quarterly):** download the latest archive, restore the
  SQL dump into a scratch database, point a staging `.env` at it, run the
  test suite's smoke checks (`/admin/login` reachable, partner amounts
  correct), and document the time taken.
- Backup archives must never live inside `public_html`.

## Security checklist before go-live (plan §6)

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] HTTPS forced (hPanel), `SESSION_SECURE_COOKIE=true`
- [ ] All 5 panels reachable only with the matching role (run `PanelAccessTest` against staging)
- [ ] Partner IDOR tests green (`PartnerPanelTest`)
- [ ] No backup files, `.env`, or `.git` under `public_html`
- [ ] Demo users absent (`users` table contains only real accounts)
- [ ] Security headers present (delivered by `SecurityHeaders` middleware)
- [ ] Rate limiting active on `/anfrage` (throttle:10,1) + honeypot
- [ ] 2FA for admin/GM/accountant (Phase 9+ enhancement — enforce before go-live if enabled)

## Domain change plan (301 redirects)

When moving from viracarsrent.ch to the new domain:

1. Keep the old domain pointed at the same hosting.
2. Add a server-level (or `.htaccess`) 301 from the old host to the new host, preserving paths.
3. Path-level legacy redirects (old WordPress URLs → new structure) are
   managed in the admin panel under **Redirects** — they apply to any
   unmatched URL, including old permalinks.
4. Update `APP_URL`, regenerate sitemap, and submit the change of address
   in Google Search Console.

## Handover

- Admin handbook: every CMS function is in the `/admin` panel (pages with
  block builder, menus, media, blog, leads, redirects, users, settings).
- GM: `/manage` — partner amounts page is the single control point for the
  partner dashboard number; every change is in the activity log.
- Operational contacts, credentials, and the cron entry are documented in
  the hosting vault (not in this repository).
