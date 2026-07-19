# notakrista.com: The Working Bench

`www.notakrista.com` is a **digital resume replacement and a real working tool** at the same time. The public surface acts as a personal engineering bench, combining technical capability with the human signal behind the work (gaming streaks, open-source projects, and real-life activities).

---

## 🛠️ Tech Stack

This project is built using modern PHP and Laravel ecosystems:
- **PHP**: 8.5
- **Laravel Framework**: v13
- **Filament**: v5 (Panel & Table Builder)
- **Livewire**: v4
- **Flux UI**: v2
- **Tailwind CSS**: v4

---

## 🎨 Visual System

Based on the **Gruvbox** palette in OKLCH:
- **Default Visual System**: Gruvbox Dark. Retro-tech terminal feel.
- **The Gruvbox Rule**: Strictly off-palette colors are prohibited. Only gruvbox tokens style inputs, cards, and buttons.

---

## 📊 Feature Matrix

The matrix tracks what is dynamic, what remains a mockup, and what is client-side or planned. Public routes are gated by `FilamentMode::Admin` (see `routes/web.php`).

| Feature | Route | Database Table(s) | Current Status |
| :--- | :--- | :--- | :--- |
| **Welcome / Landing** | `/` | `home_phrases`, `language_lines` | ⚡ **Dynamic** (queries DB-backed subheadings by locale) |
| **Character Sheet** | `/character` | `items`, `categories` | ⚡ **Dynamic** (queries equipped items by loadout configuration) |
| **Inventory Bag** | `/inventory` | `items`, `categories` | ⚡ **Dynamic** (queries DB, groups items by category) |
| **Todoticket Calculator** | `/todoticket` | None | 🛠️ **Client-Side Tool** (Alpine.js calculator) |
| **Home Phrases (Admin)** | *None* | `home_phrases` | 🔑 **Admin Only** (Filament resource) |
| **Locales (Admin)** | *None* | `locales` | 🔑 **Admin Only** (Filament resource) |
| **Translations (Admin)** | *None* | `language_lines` | 🔑 **Admin Only** (Filament resource) |
| **Skills & Talents** | `/skills` | None | 📱 **Mockup** (hardcoded Alpine lists) |
| **Stats & Achievements** | `/stats` | None | 📱 **Mockup** (hardcoded Alpine lists) |
| **The Foundry** | `/foundry` | None | 📱 **Mockup** (hardcoded projects array) |
| **Donations (Public)** | `/donations` | `accounts` (donation fields) | ⚡ **Dynamic** (accounts with any donation field set) |
| **Budget Transparency (Public)** | `/budget` | `transactions`, `transaction_categories`, `accounts` | ⚡ **Dynamic, aggregate-only** (USD; `is_public = true` only; no account names, no transaction detail) |
| **Accounts (Admin)** | `/admin` | `accounts` | 🔑 **Admin Only** (Filament resource; donation info on each account) |
| **Budget Dashboard (Admin)** | `/admin/budget` | `transactions`, `transaction_categories`, `schedules` | 🔑 **Admin Only** (custom `BudgetPage` with month switcher, KPI cards, category breakdown, due-schedule count) |
| **Transaction Categories (Admin)** | `/admin` | `transaction_categories` | 🔑 **Admin Only** (Filament resource; 15 default categories) |
| **Transactions (Admin)** | `/admin` | `transactions` | 🔑 **Admin Only** (Filament resource, `is_public` toggle, mark-posted row action) |
| **Schedules (Admin)** | `/admin` | `schedules` | 🔑 **Admin Only** (Filament resource, `Post now` action, auto-post console command) |
| **Calendar** | *None* | None | ⏳ **Planned** (no schema yet) |
| **Wishlist** | *None* | None | ⏳ **Planned** (no schema yet) |

The full breakdown of the budget/finance domain lives in the next section.

---

## 💸 Budgeting, Finance & Donations

This domain is built around a single rule: **the only currency that matters in the books is USD; bank/exchange labels are display only.**

### Data model

| Model | Purpose | Notable fields |
| :--- | :--- | :--- |
| `Account` | A bank, exchange, wallet, or cash account. Holds donation info for any account that accepts contributions. | `name`, `type` (Bank, Exchange, Wallet, Cash, Other), `currency` (bank label: USD, VES, USDT, EUR; display only, not used in math), `opening_balance`, `donation_url`, `donation_address`, `donation_instructions`, `donation_qr_image` |
| `TransactionCategory` | A simple flat category (e.g. payroll, rent, groceries, subscriptions, tools, earthquake-relief). 15 seeded. | `slug`, `name`, `icon`, `color_token`, `position` |
| `Transaction` | An income or expense against an account, optionally categorized, optionally public. | `account_id`, `category_id?`, `amount` (USD decimal), `direction` (inflow / outflow), `occurred_on` (date), `posted_at?` (date; null means unposted or pending), `is_public` (bool), `payee_name?` (free text), `memo?` |
| `Schedule` | A recurring template. When due, the `PostScheduleAction` materializes a `Transaction` and advances `next_run_on` by one cadence. | `account_id`, `category_id?`, `amount`, `direction`, `cadence` (Weekly, Biweekly, Monthly, Bimonthly, Quarterly, Yearly, Once), `next_run_on`, `last_run_on?`, `auto_post` (bool), `is_active` (bool) |

#### `posted_at` vs the old `status` enum

Transactions no longer carry a `status` enum (pending/cleared/reconciled). Instead:
- `posted_at IS NULL` → transaction is pending (unposted)
- `posted_at IS NOT NULL` → transaction is posted at that date

The admin table has a **Mark posted** row action (idempotent) and a **Mark unposted** action. A schedule with `auto_post = true` produces posted transactions when it fires; with `auto_post = false` the user confirms manually.

#### `is_public` flag

Every transaction carries an `is_public` toggle. The public `/budget` page only shows `is_public = true` transactions, and only their **aggregates** (totals + category breakdown). No transaction detail, no account names, no payee names. The public page never reveals the underlying books. The user selects which transactions to disclose.

### Public surfaces (no auth)

- **`GET /donations`** at `app/Http/Controllers/DonationsController.php`. Lists every active `Account` that has any of `donation_url`, `donation_address`, or `donation_qr_image` set. Each card shows: name, currency label, optional URL, optional address, optional 176x176 lazy `<img>` for `qr_image`, optional instructions.
- **`GET /budget`** at `app/Http/Controllers/BudgetTransparencyController.php`. Accepts `?month=YYYY-MM` (validated against `/^\d{4}-\d{2}$/`, falls back to current month). Calls `PublicBudgetSnapshot::for($month)` and renders the aggregate-only view.
- **Public budget view** (`resources/views/budget.blade.php`) renders:
  - Three KPI cards: **Net / Income / Spent** (USD, computed from `is_public = true` transactions in the selected month).
  - One row per non-zero `TransactionCategory` with **Spent** total.
  - **Last 3 months** strip.
  - A donation CTA linking to `/donations`.
- **Donations page** preserves the glitch-effect title (Alpine) with a `prefers-reduced-motion` short-circuit.

### Back-office surface (Filament, `/admin`)

All resources follow the standard `#[Override]` boilerplate: `$model`, `$navigationIcon`, `$navigationSort`, and `$isScopedToTenant = false`. `getNavigationGroup()` returns `__('menu.nav_group.finance')` or `__('menu.nav_group.budget')`. `getModelLabel()` returns `__('resources.<name>')`.

| Resource | Sort | Group | Icon | Notable actions |
| :--- | :--- | :--- | :--- | :--- |
| `AccountResource` | 600 | Finance | `Wallet` | standard CRUD; donation URL / address / instructions / QR are managed on the same form. |
| `BudgetPage` (custom) | 600 | Budget | `Wallet` | Month switcher, Income / Spent / Net cards, category breakdown, due-schedule count. |
| `TransactionCategoryResource` | 700 | Budget | `Tag` | Simple CRUD with icon and color_token. |
| `TransactionResource` | 801 | Budget | `ArrowsRightLeft` | **Mark posted** / **Mark unposted** row actions, `is_public` toggle. |
| `ScheduleResource` | 803 | Budget | `Clock` | **Post now** row action, cadence, auto_post toggle. |

### Services

Two readonly services in `app/Services/Budget/`:

- **`MonthlySummaryService`** (`app/Services/Budget/MonthlySummaryService.php`): aggregates the requested month for either the admin view (all transactions) or the public view (`is_public = true` only). Returns `{year_month, month_label, income, spent, net, transaction_count, categories[{slug,name,icon,color_token,spent,count}], previous_months[3]}`.
- **`PublicBudgetSnapshot`** (`app/Services/Budget/PublicBudgetSnapshot.php`): the **only** thing the public route may read. Returns `{year_month, month_label, display_currency:"USD", totals{income,spent,net}, categories, previous_months, donation_accounts}`. No FX conversion. No group breakdown. No payees.

### The transaction pipeline

`CreateTransactionAction` (`app/Actions/Transactions/CreateTransactionAction.php`) is the single entry point for writing a `Transaction`. It accepts `{account_id, amount, direction, occurred_on, category_id?, payee_name?, memo?, is_public?, posted_at?}` and persists the row. No `Payee` model, no rule engine, and no auto-categorization exist. Categorization is user-driven.

`MarkTransactionPostedAction` (`app/Actions/Transactions/MarkTransactionPostedAction.php`) wraps `Transaction::markPosted()`. Idempotent.

### The schedule engine

`Schedule` rows are templates. `Schedule::markRan(DateTimeInterface)` stamps `last_run_on` and advances `next_run_on` by one `BillCadence::advance()` interval.

- **`PostScheduleAction`** (`app/Actions/Schedules/PostScheduleAction.php`): materializes a `Transaction` from a `Schedule`, sets `posted_at` to today if `auto_post = true`, then calls `markRan()`. Returns the created `Transaction` (or `null` if the schedule is inactive).
- **`PostDueSchedules`** console command (`app/Console/Commands/PostDueSchedules.php`): signature `budget:post-schedules {--dry-run}`. Picks every active schedule where `next_run_on <= today` and hands each to `PostScheduleAction`. Wired in `routes/console.php` to run daily at `06:00` America/Costa_Rica.

### Seeded data

A new migration `2026_07_17_100600_seed_default_accounts_with_donation_info.php` seeds five default accounts with their pre-existing donation details:

- **Facebank (Puerto Rico)**: USD bank with US transfer details.
- **Bancamiga**: VES bank with Pago Móvil QR.
- **PayPal**: USD wallet with `paypal.me/akristax`.
- **Binance**: USDT exchange with Binance Pay ID, USDT TRC-20 wallet, and QR code.
- **BDV**: VES bank with Banco de Venezuela account.

The donation page picks these up automatically because their `donation_address` / `donation_url` / `donation_qr_image` columns are non-null.

### Permissions & multi-tenancy

- All Filament resources in this domain have `#[Override] protected static bool $isScopedToTenant = false`. Team scoping does not apply.
- Permissions are migration-seeded under the `<action>_<resource>` convention. The cleanup migration `2026_07_17_100500_cleanup_orphan_lookups.php` removed permissions for resources that no longer exist (`bill`, `debt`, `budget_category`, `budget_category_group`, `budget_allocation`, `payee`, `transaction_rule`, `donation_channel`) and deleted their `language_lines` rows.
- The `admin` role is granted every permission by the seeding migrations.
- The test helper `budgetAdmin($permissions)` in `tests/Pest.php:81` creates a user on a personal team, seeds the requested permission names, assigns the `admin` role, and switches the Filament panel tenant.

### Tests

Covered by feature tests in `tests/Feature/`:

- `Services/PublicBudgetSnapshotTest.php`: tests public snapshot shape, `is_public` filtering, donation accounts list, month switcher, and aggregate-only HTTP assertions.
- `Actions/TransactionsTest.php`: tests create action, mark posted, mark unposted, inflow/outflow scopes, and category attachment.
- `Actions/PostScheduleActionTest.php`: tests that posting a schedule creates a posted transaction, advances `next_run_on`, skips inactive schedules, supports inflow schedules, and respects `is_due` and `dueOnOrBefore` scopes.
- `Filament/AccountResourceTest.php`: tests that the list page renders for admins, account creation persists donation info, and `hasDonationInfo()` returns true when any donation field is set.
- `Filament/TransactionResourceTest.php`: tests that the list page renders and supports mark posted and mark unposted actions.
- `Filament/TransactionCategoryResourceTest.php`: tests that the list page renders and supports category creation.
- `Filament/Phase3ResourceTest.php`: tests that the schedule list page renders and supports schedule creation.
- `GuestPagesTest.php`: tests that every public route (`/`, `/todoticket`, `/character`, `/inventory`, `/skills`, `/stats`, `/donations`, `/budget`, `/foundry`) returns 200 with the expected text. The `/donations` page lists donation-flagged accounts. The `/budget` page exposes "Budget transparency".

> **Note on test execution:** the project's `RefreshDatabase` trait writes to the dev `database/database.sqlite` file before swapping to in-memory for each test, so running multiple test groups in separate processes against a long-lived dev DB can corrupt state. If a test group fails with `SQLSTATE database disk image is malformed`, delete `database/database.sqlite` and re-run that group on a clean dev DB.

---

## 📝 Roadmap & TODO List

Organized by stage. Items below are the real backlog, not aspirations.

### Stage 1: Database Integration (Migrating Mockups)
- [ ] **Skills & Talents**: Create a `skills` table, seed it, register the Filament resource, query dynamically on `/skills`.
- [ ] **Stats & Achievements**: Replace the hardcoded Alpine block with a stats engine (Wakatime, commit count, server uptime, or whichever source wins).
- [ ] **The Foundry**: Create a `projects` table, Filament resource, and query dynamically on `/foundry`.

### Stage 2: Design & Styling Alignment
- [ ] **Loud Emergency Alert Box**: Style the Venezuela earthquake relief alert on `/donations` and the BudgetPage to use a loud Terminal Red visual layout at rest (per `DESIGN.md`).
- [ ] **Keyboard Navigability**: Ensure the theme switcher, donations glitch effect, and BudgetPage allocation editors are fully keyboard reachable with the focus-ring signature.
- [ ] **Bilingual Coverage on `/donations` and `/budget`**: The `x-text` ternaries are inline. Long-term, move them into `language_lines` so they are translatable through the admin panel like the rest of the system.

### Stage 3: New Feature Implementation
- [ ] **Calendar**: Create a calendar schema (events, attendees, time slots), coordinate reservation/intake flow, render a front-end view.
- [ ] **Wishlist**: Create a wishlist model (item, url, price, status), render cards.
- [ ] **Policy classes for Phase 2-3 resources**: `PayeePolicy`, `TransactionPolicy`, `TransactionRulePolicy`, `SchedulePolicy`, `DonationChannelPolicy` are no longer applicable (those resources were removed). Add `TransactionCategoryPolicy` if needed.
- [ ] **Reconciliation workflow**: The "Reconcile" action on `TransactionResource` plus a per-account reconciliation view is out of scope now that `cleared_at` / `reconciled_at` are gone. If reconciliation returns, it's a fresh design.

### Stage 4: Production & Verification
- [ ] Run production asset build (`bun run build`).
- [ ] Deploy and verify the full suite on production.
- [ ] Audit accessibility (WCAG 2.2 AA contrast ratios, `prefers-reduced-motion` already respected by the donations glitch effect and the budget donut, but a full pass is owed).

---

## 🚀 Local Development Setup

To run this project locally, ensure you have **Laravel Herd** or PHP 8.5/Composer installed.

1. **Install Composer Dependencies**:
   ```bash
   composer install
   ```

2. **Install Frontend Dependencies**:
   ```bash
   bun install
   ```

3. **Configure Environment**:
   Copy `.env.example` to `.env` and configure your database settings.

4. **Run Database Migrations & Seeds**:
   ```bash
   php artisan migrate --seed
   ```

5. **Start Dev Server**:
   If using Herd, the site is served at `http://notakrista.com.test`. Run the Vite compiler in parallel:
   ```bash
   bun run dev
   ```
