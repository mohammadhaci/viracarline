# DEPLOYMENT.md — Hostinger Shared Hosting (stub)

> Phase 0 stub. Full deployment happens in Phase 10 (see plan.md §7).
> This documents the target layout and constraints so all phases build for it.

## Target layout

```
/home/USER/vira-platform        ← app root (git deploy)
/home/USER/public_html          ← contents of /public (or symlink if plan allows)
storage/app/private             ← partner docs, NEVER under public_html
```

The application root lives OUTSIDE `public_html`. Only the contents of
Laravel's `/public` directory are web-accessible. Private partner documents
(statements, contracts, receipts) are stored on the private disk under
`storage/app/private` and served exclusively through signed URLs — never
copied or linked into the webroot.

## hPanel checklist

1. **PHP version:** select PHP 8.3 in hPanel (per-site PHP configuration).
2. **Database:** create a MySQL 8 database + user; put credentials in `.env`.
3. **`.env`:** never commit it. Copy `.env.example`, set production values,
   `APP_ENV=production`, `APP_DEBUG=false`, generate `APP_KEY` with
   `php artisan key:generate`.
4. **Storage symlink:** run `php artisan storage:link` once after deploy
   (only links the *public* disk; the private disk stays unlinked by design).
5. **Cron (single entry — runs scheduler AND queue):**

   ```
   * * * * * php /home/USER/vira-platform/artisan schedule:run >> /dev/null 2>&1
   ```

   The scheduler runs `queue:work --stop-when-empty --max-time=50` every
   minute (see `routes/console.php`), so no long-lived worker process is
   needed — this is the shared-hosting queue pattern.

## Shared-hosting constraints baked into the app

- `QUEUE_CONNECTION=database` — no Redis available.
- `SESSION_DRIVER=database`, `CACHE_STORE=database`.
- Image conversions are queued and use modest sizes to respect memory limits
  (relevant from Phase 2 onward).
- No backup archives or `.env` files inside the webroot, ever.
