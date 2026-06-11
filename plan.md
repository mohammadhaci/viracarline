# plan.md — Vira Car Lines AG Platform

**Project:** Complete redesign + rebuild of viracarsrent.ch (domain will change later)
**Company:** Vira Car Lines AG (Switzerland)
**Business:** Buying, selling, and repairing cars (NOT rental — current domain is misleading)
**Type:** Public multilingual website + 5 role-based back-office panels (CMS + ERP)

-----

## 1. Goals

1. Replace the current broken WordPress site with a modern, fast, multilingual public website (DE / FR / EN).
1. Build a unified back-office on one codebase and one database, split into 5 panels by role:
- Admin (full CMS, WordPress-level control of the website)
- General Manager / CEO
- Mechanic (workshop)
- Partner (investors)
- Accountant
1. **Critical business feature:** Every partner sees a prominent CHF amount on their dashboard. The General Manager controls this amount from his panel — a global default value plus an optional per-partner override.

-----

## 2. Tech Stack

|Layer            |Choice                                                                         |Reason                                                                  |
|-----------------|-------------------------------------------------------------------------------|------------------------------------------------------------------------|
|Framework        |Laravel 12 (PHP 8.3+)                                                          |Mature, fits Hostinger shared/VPS hosting, team familiarity             |
|Back-office      |Filament v4 (latest stable)                                                    |Native multi-panel support; each role gets its own panel + URL + guard  |
|Auth/Roles       |Laravel auth + `spatie/laravel-permission`                                     |Role + permission matrix across panels                                  |
|Database         |MySQL 8                                                                        |Hostinger standard                                                      |
|Public frontend  |Blade + Tailwind CSS + Alpine.js (+ Livewire where interactive)                |Fast, SEO-friendly server-rendered pages                                |
|i18n             |`spatie/laravel-translatable` (model content) + Laravel lang files (UI strings)|DE / FR / EN; URL prefix routing `/de`, `/fr`, `/en`                    |
|Media            |`spatie/laravel-medialibrary`                                                  |Conversions/thumbnails for car galleries                                |
|PDF              |`barryvdh/laravel-dompdf` or `spatie/laravel-pdf`                              |Contracts, invoices, reports                                            |
|Activity log     |`spatie/laravel-activitylog`                                                   |Audit trail (who changed what — required for partner amounts & finances)|
|Backups          |`spatie/laravel-backup`                                                        |Nightly DB + media backup                                               |
|Search/Filters   |Native Eloquent + Filament tables                                              |No external search engine needed at this scale                          |
|Testing          |Pest                                                                           |Feature tests per phase                                                 |
|Security scanning|Semgrep in CI                                                                  |Per standard workflow                                                   |
|Hosting          |Hostinger (existing)                                                           |Deploy via Git + Hostinger; queue via cron-based worker                 |

Default panel paths (single app, single login page per panel):

```
/admin      → Admin panel (CMS + system)
/manage     → General Manager panel
/workshop   → Mechanic panel
/partner    → Partner panel
/finance    → Accountant panel
```

-----

## 3. Roles & Panels

### 3.1 Admin Panel — `/admin` (WordPress-level CMS)

Full control of the public website + system administration.

- **Pages & Block Builder:** Page CRUD with a block-based content builder (Filament Builder field). Block library (initial ~20 blocks): hero, car grid, car slider, feature list, stats counters, image+text, gallery, testimonials, team, FAQ accordion, CTA banner, contact form, map, services grid, pricing/finance teaser, partner logos, video embed, rich text, spacer, custom HTML.
- **Navigation:** Menu builder (header, footer, multiple menus), drag & drop ordering, per-language labels.
- **Media Library:** Central uploads, folders, alt-text per language, automatic image conversions (webp, thumbnails).
- **Vehicle Listings (public side):** Publish/unpublish vehicles to the website, featured flags, ordering, listing photos, public price.
- **Blog/News:** Posts, categories, per-language content.
- **Leads Inbox:** Contact form + vehicle inquiry submissions, status (new/contacted/closed), assign to user.
- **SEO:** Per-page meta title/description per language, OG image, sitemap.xml auto-generation, hreflang tags, redirects manager (important after domain change), robots settings.
- **Translations:** Side-by-side DE/FR/EN editing on every translatable record; UI string editor.
- **Users & Roles:** Create users, assign roles (admin, gm, mechanic, partner, accountant), suspend, force password reset, 2FA management.
- **Site Settings:** Logo, company data (Vira Car Lines AG, address, UID/CHE number), opening hours, social links, scripts (analytics), maintenance mode.
- **System:** Activity log viewer, backup status.

### 3.2 General Manager Panel — `/manage`

Executive cockpit + the partner amount control.

- **Dashboard KPIs:** Cars in stock (count + total purchase value), cars sold this month, gross margin per sold car, average days-in-stock, workshop revenue, open repair orders, pending leads, total partner capital.
- **Partner Display Amount (core feature):**
  - Setting: `partner_display_amount_default` (CHF, decimal) — applies to ALL partners.
  - Per-partner override: nullable `display_amount_override` on the partner record.
  - Effective value shown to a partner = `override ?? default`.
  - Optional label/note field shown next to the number (e.g., “Stand: Juni 2026”).
  - Full audit log: every change records user, timestamp, old → new value (activitylog).
  - UI: one dedicated page “Partner Amounts” with the global field on top + a table of all partners, their override (inline editable), and the effective value column.
- **Vehicle Trading Overview:** Approve/record purchases (Ankauf) and sales (Verkauf), see margin per vehicle (purchase price + reconditioning costs vs. sale price).
- **Partner Management:** Create partners, link vehicles/capital to partners, see per-partner summaries.
- **Reports:** Monthly P&L summary, inventory value, top models by margin, export CSV/PDF.
- **Approvals (optional, later phase):** Purchases above a threshold require GM approval.

### 3.3 Mechanic Panel — `/workshop`

Workshop operations for both internal reconditioning and customer repairs.

- **Repair Orders (Reparaturaufträge):**
  - Two types: `internal` (recondition a purchased car before sale) and `customer` (external repair service).
  - Fields: vehicle (from stock or customer-owned), reported issues, diagnosis, tasks checklist, parts used, labor hours, status (open → in_progress → waiting_parts → done → invoiced), photos before/after.
  - Internal orders automatically add their cost to the vehicle’s total cost (affects margin).
  - Customer orders generate a draft invoice for the accountant.
- **Purchase Inspection (Ankaufscheck):** Standard checklist when a car is bought (body, engine, electronics, tires, interior, accident history) with photos; result attached to the vehicle record.
- **Parts Inventory:** Parts list with stock quantity, min-stock alert, cost price; consuming a part in a repair order decrements stock.
- **My Day View:** Kanban or simple list of orders assigned to the mechanic, sorted by priority.
- No access to finances, partner data, or CMS.

### 3.4 Partner Panel — `/partner`

Read-mostly portal for investors.

- **Top of dashboard — the CHF card (the most important element):**
  - A large, prominent card showing the effective amount: e.g. **CHF 125’000.00** (Swiss number formatting with apostrophe thousands separator), plus the optional note set by the GM.
  - This is the first thing every partner sees after login. Non-dismissable, always on top.
- **My Vehicles:** Vehicles linked to this partner with status (in stock / in workshop / listed / sold) and sale results where applicable.
- **Statements:** Periodic statements/documents uploaded by GM or accountant (PDF list with download).
- **Profile:** Own contact data, password, 2FA.
- Strict scoping: a partner can NEVER see other partners, global financials, or the override mechanics — only their own effective number.

### 3.5 Accountant Panel — `/finance`

- **Invoices:** Sales invoices (vehicle sales) and workshop invoices (customer repairs); QR-bill ready layout (Swiss QR-Rechnung) as a later enhancement, standard PDF invoice in v1.
- **Swiss VAT (MWST):** Configurable rates (standard 8.1%), per-line VAT, VAT summary report per quarter.
- **Purchases & Expenses:** Record vehicle purchase costs, parts purchases, general expenses with categories and receipt upload.
- **Payments:** Mark invoices paid (date, method), open items list (Debitoren), simple dunning status.
- **Partner Payouts:** Record payouts to partners (amount, date, reference) — visible to GM, summarized to the partner as documents.
- **Reports & Export:** Revenue/expense reports by period, CSV export compatible with Swiss accounting tools (Banana/Bexio import format), vehicle margin report.

-----

## 4. Database Schema (core tables)

```
users                 id, name, email, password, locale, is_active, 2fa columns
roles / permissions   (spatie)

settings              key, value (json)            ← partner_display_amount_default, amount_note, site settings

partners              id, user_id, company_name, contact, display_amount_override (decimal, nullable),
                      override_note (nullable), joined_at, is_active

vehicles              id, vin, brand, model, variant, year, mileage_km, fuel, transmission, color,
                      purchase_price, purchase_date, purchase_source,
                      asking_price, sold_price (nullable), sold_at (nullable),
                      status enum: purchased | in_workshop | ready | listed | reserved | sold | archived,
                      partner_id (nullable FK), is_published, is_featured, slug
vehicle_translations  vehicle_id, locale, title, description, seo fields
vehicle_costs         id, vehicle_id, type (repair|transport|fees|other), amount, note, repair_order_id (nullable)

customers             id, type (buyer|repair_client|seller), name, contact, address, language
vehicle_sales         id, vehicle_id, customer_id, price, vat_mode, contract_pdf_path, sold_at
vehicle_purchases     id, vehicle_id, seller (customer_id or free text), price, purchased_at, inspection_id

repair_orders         id, number, type (internal|customer), vehicle_id (nullable), customer_id (nullable),
                      customer_vehicle_info (json, for external cars), assigned_to (user_id),
                      status, priority, diagnosis, started_at, finished_at
repair_tasks          id, repair_order_id, description, is_done, labor_hours
repair_parts          id, repair_order_id, part_id, qty, unit_cost
parts                 id, sku, name, stock_qty, min_qty, cost_price, sale_price

inspections           id, vehicle_id, checklist (json), result, inspected_by, photos

invoices              id, number, type (vehicle_sale|repair), customer_id, lines (related table),
                      subtotal, vat_rate, vat_amount, total, currency=CHF, status, due_at, paid_at, pdf_path
invoice_lines         id, invoice_id, description, qty, unit_price, vat_rate
expenses              id, category, amount, vat_amount, date, vendor, receipt_path, vehicle_id (nullable)
partner_payouts       id, partner_id, amount, date, reference, note, created_by

pages                 id, slug, template, blocks (json), is_published, menu settings
page_translations     page_id, locale, title, blocks_content (json), seo fields
menus / menu_items    per-language labels, ordering
posts                 blog (translatable)
leads                 id, type (contact|vehicle_inquiry), vehicle_id (nullable), name, email, phone,
                      message, locale, status, assigned_to
media                 (spatie medialibrary tables)
activity_log          (spatie activitylog — mandatory on settings, partners, invoices, vehicles)
```

**Partner amount resolution (single source of truth):**

```php
// PartnerAmountService
public function effectiveAmountFor(Partner $partner): Money
{
    return $partner->display_amount_override
        ?? (decimal) Setting::get('partner_display_amount_default', 0);
}
```

Formatting helper: Swiss style `CHF 125'000.00` via `NumberFormatter('de_CH')`.

-----

## 5. Public Website (DE / FR / EN)

- **Routing:** `/{locale}/...` with locale middleware; default `de`; language switcher keeps current page; hreflang in head.
- **Pages (initial):** Home, Fahrzeuge (vehicle listing with filters: brand, price, year, fuel, transmission, mileage), vehicle detail (gallery, specs table, inquiry form), Ankauf (“we buy your car” form with photo upload), Werkstatt/Services, About (über uns), Contact (form + map), Blog, Impressum, Datenschutz (Swiss revDSG-compliant privacy page), AGB.
- **Vehicle detail page:** Photo gallery, full spec sheet, price in CHF, “Anfrage senden” inquiry form → creates a lead, optional WhatsApp/phone CTA, similar vehicles.
- **Design direction:** Premium automotive look — dark hero with featured car, clean white listing sections, CHF-prominent pricing, real photography over stock. Fully responsive, Core Web Vitals-friendly (server-rendered, lazy images, webp).
- **SEO:** Per-page meta from CMS, structured data (`Vehicle` / `Car` schema.org on listings, `AutoRepair` + `AutoDealer` LocalBusiness on home), sitemap per language, 301 redirect manager (critical for the upcoming domain change).

-----

## 6. Security Checklist

- All panels behind auth + role middleware; panel access enforced at Filament panel level AND policy level (defense in depth).
- Partner data scoping via global query scopes — partner panel queries are always scoped to `auth()->user()->partner_id`. Write Pest tests proving partner A cannot see partner B (IDOR tests).
- 2FA available for all back-office users; enforced for GM + Accountant + Admin.
- Rate limiting on login + public forms; honeypot + optional Turnstile on lead forms.
- Signed URLs for private documents (statements, contracts, receipts); media for partners stored on private disk, never in `/public`.
- Activity log on: settings changes, partner amount changes, invoice changes, vehicle price changes, user/role changes.
- File upload validation (mime, size), images re-encoded on upload.
- CSP headers, HTTPS-only cookies, `APP_DEBUG=false` in production, no backup files in webroot (lesson learned from Vorarlberg audit).
- Semgrep + `composer audit` in CI.

-----

## 7. Phases

### Phase 0 — Foundation

Laravel 12 project, Filament v4, MySQL, Spatie permission/medialibrary/translatable/activitylog/backup, Pest, CI (lint + tests + Semgrep), `.env` structure, the 5 empty panels with auth + role gates, seeders for the 5 demo users.
**Done when:** each role logs into its own panel and is blocked from the other four (tested).

### Phase 1 — Core Domain Models

Migrations + models + factories for vehicles, partners, customers, settings, costs. PartnerAmountService + Swiss money formatting helper + unit tests.
**Done when:** schema migrated, factories seed a realistic demo dataset, amount-resolution tests pass.

### Phase 2 — Admin CMS

Pages with block builder, menus, media library, blog, translations workflow (DE/FR/EN side-by-side), site settings, users & roles UI, leads inbox, redirects manager.
**Done when:** a non-technical admin can build and publish a multilingual page without code.

### Phase 3 — Public Website

Blade + Tailwind frontend, locale routing, all initial pages, vehicle listing + filters + detail + inquiry form, SEO layer (meta, schema.org, sitemap, hreflang), Impressum/Datenschutz/AGB templates.
**Done when:** site is fully browsable in 3 languages with seeded vehicles and passes Lighthouse ≥ 90 mobile.

### Phase 4 — GM Panel + Partner Amount System

KPI dashboard, “Partner Amounts” page (global default + per-partner overrides + audit trail), partner management, vehicle trading overview with margin calculation.
**Done when:** GM changes the global amount and one override; both are audited; effective values verified.

### Phase 5 — Partner Panel

CHF hero card (effective amount + note), my vehicles, statements/documents (private disk + signed URLs), profile. IDOR scoping tests.
**Done when:** partner sees the exact number the GM set, instantly after change, and nothing beyond their scope.

### Phase 6 — Workshop / Mechanic Panel

Repair orders (internal + customer), tasks, parts inventory with stock decrement + min-stock alerts, purchase inspections with photo checklists, internal order costs feeding `vehicle_costs`.
**Done when:** an internal repair raises the vehicle’s total cost and a customer repair produces a draft invoice.

### Phase 7 — Trading Flow (Ankauf / Verkauf)

Purchase recording (+ inspection link), publishing pipeline (purchased → workshop → ready → listed → sold), sales recording with customer + contract PDF generation, margin report per vehicle.
**Done when:** full lifecycle of one car runs end-to-end with correct margin.

### Phase 8 — Accountant Panel

Invoices (vehicle + repair) with PDF, MWST 8.1% handling, expenses with receipts, payments/open items, partner payouts, CSV exports, quarterly VAT summary.
**Done when:** a sold car and a customer repair both produce correct invoices and appear in period reports.

### Phase 9 — Polish & Notifications

Email notifications (new lead → admin, status updates), dashboard refinements, empty states, Arabic-friendly admin option (optional), performance pass (caching, eager loading audit), accessibility pass.

### Phase 10 — Audit & Go-Live

Full security audit against the checklist in §6, backup + restore drill, deployment runbook for Hostinger (Git deploy, cron for scheduler/queue, storage symlink), staging → production, 301 redirect plan for the future domain change, handover docs.

-----

## 8. Resolved Decisions (2026-06-11)

1. **Hosting: Hostinger SHARED.** Consequences baked into the architecture:
- `QUEUE_CONNECTION=database`, worker via cron (`queue:work --stop-when-empty` every minute), no Redis.
- `SESSION_DRIVER=database`, `CACHE_STORE=database`.
- App root deployed OUTSIDE `public_html`; `public_html` points to Laravel `/public` (symlink or copied docroot). Private partner documents live in `storage/app/private` — never web-accessible.
- Image conversions queued, modest sizes to respect shared-hosting memory limits.
1. **Public prices:** per-vehicle toggle `show_price`. When off → “Preis auf Anfrage” / “Prix sur demande” / “Price on request”.
1. **Swiss QR-Rechnung (QR-bill):** v2. v1 ships standard PDF invoices with full MWST handling.
1. **Partner statements:** v1 = PDFs uploaded by GM/accountant to the partner’s document area. v2 = system-generated.
1. **Branding: KEEP existing visual identity.** Reuse the current Vira logo; extract its palette as Tailwind design tokens and apply across the public site and all 5 Filament panels (logo + primary color per panel).
