# AGENTS.md

## Project Overview
- Laravel 9 + PHP 8.0 app with Jetstream, Sanctum, Livewire, Alpine, Vite, Tailwind, and PHPUnit.
- Purpose: receive client tickets from the app or an external system, assign them to developers, and track work through tasks, statuses, priorities, receipts, proposals, and attachments.
- The app has both web CRUD flows and authenticated REST API endpoints.
- No repo-local Cursor rules, `.cursorrules`, or `.github/copilot-instructions.md` were found; use this file as the main agent guide.

## Stack And Layout
- Backend: `app/Models`, `app/Http/Controllers`, `app/Http/Controllers/Api`, `app/Http/Requests`, `app/Policies`, `app/Http/Resources`.
- Frontend: Blade + Livewire + Alpine in `resources/views`, `app/Http/Livewire`, and `resources/js`.
- Data and tests: `database/factories`, `database/seeders`, `tests/Feature`, `tests/Unit`.
- Routing: `routes/web.php` for browser flows, `routes/api.php` for Sanctum-protected API routes.

## Key Domain Notes
- Preserve legacy names already embedded across the app: `Statu`, `Rol`, `Ticket`, `Task`, `Developer`, `Client`, `Person`, `Receipt`, `Proposal`, `Priority`, `Attachment`.
- Do not rename these terms casually; they are referenced by models, factories, policies, routes, and tests.
- Keep web controllers and API controllers separate.
- When changing list/search behavior, check `App\Models\Scopes\Searchable`.
- When changing auth behavior, verify Policies and Sanctum middleware together.

## Setup Commands
Run from repo root:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Useful local dev commands:

```bash
php artisan serve
npm run dev
```

## Build, Lint, And Test Commands

### Frontend
```bash
npm run dev
npm run build
```

### PHP formatting / linting
```bash
./vendor/bin/pint
./vendor/bin/pint app tests routes database
php -l app/Models/Ticket.php
```

- `./vendor/bin/pint` is the standard PHP formatter/linter for this repo.
- There is no repo-specific ESLint or Prettier config; keep JS changes minimal and aligned with the existing Vite/Alpine style.

### Full test suite
```bash
php artisan test
./vendor/bin/phpunit
```

### Run a single test file
```bash
php artisan test tests/Feature/Api/TicketTest.php
./vendor/bin/phpunit tests/Feature/Api/TicketTest.php
```

### Run a single test method
```bash
php artisan test --filter=it_updates_the_ticket
php artisan test tests/Feature/Api/TicketTest.php --filter=it_updates_the_ticket
./vendor/bin/phpunit --filter it_updates_the_ticket tests/Feature/Api/TicketTest.php
```

### Run a test class by name
```bash
php artisan test --filter=TicketTest
./vendor/bin/phpunit --filter TicketTest
```

### Test environment notes
- `phpunit.xml` sets `APP_ENV=testing`, array cache/session, sync queue, and array mailer.
- SQLite in-memory config is present but commented out; do not assume tests use SQLite.
- Many API tests use `RefreshDatabase`, factories, permission seeders, and Sanctum acting users.

## Code Style Guidelines

### General PHP / Laravel style
- Follow Laravel conventions first, then existing repo patterns.
- Use 4 spaces, UTF-8, LF endings, and a final newline; `.editorconfig` is authoritative.
- One class per file; file name must match class name.
- Do not introduce `declare(strict_types=1);` into isolated files; the repo does not use it.
- Keep methods focused and small.
- Prefer framework helpers like `route()`, `view()`, `redirect()`, and `response()->noContent()` over custom plumbing.

### Imports
- Import concrete classes at the top of the file instead of fully qualifying them inline, unless the file already uses a tiny one-off inline reference.
- Remove unused imports.
- Avoid large import reordering diffs unless you are already editing the file heavily.
- Existing import order is not perfectly normalized; optimize for readability, not churn.

### Naming
- Use PascalCase for classes, controllers, requests, policies, resources, seeders, and factories.
- Use camelCase for variables, methods, relation names, and request keys.
- Use snake_case for database columns and foreign keys.
- Use singular model names and plural REST resources/routes.
- Keep relation method names semantically clear: singular for `belongsTo`, plural for to-many relations.

### Controllers
- Keep controllers thin: authorize, validate, perform the action, return a view/resource/redirect.
- Use `FormRequest` classes for store/update validation.
- Call `$this->authorize(...)` explicitly in CRUD actions, matching existing controllers.
- Web controllers should return views or redirects with flash messages.
- API controllers should return resource classes or proper Laravel JSON responses with correct status codes.

### Validation
- Put rules in `app/Http/Requests/*StoreRequest.php` and `*UpdateRequest.php`.
- Prefer `$validated = $request->validated();` before create/update calls.
- Keep `exists:<table>,id` rules explicit and re-check legacy table names like `status` when editing validation.

### Models and Eloquent
- Define `$fillable` explicitly.
- Add `$casts` for dates or other special types when needed.
- Keep relationships on models, not in controllers.
- Reuse factories for test setup instead of hard-coding foreign keys.
- Reuse the `Searchable` trait for search-driven listings.

### Authorization
- Policies are core to the app; update the matching policy when behavior changes.
- `AuthServiceProvider` auto-discovers policy names and grants super-admin bypass through `Gate::before`.
- Do not bypass authorization checks without a documented reason.

### Error handling
- Prefer Laravel-native behavior: validation exceptions, authorization failures, model binding 404s, and standard HTTP responses.
- Do not swallow exceptions silently.
- For web flows, favor redirect + flash feedback.
- For API flows, return semantically correct status codes and resource payloads.

### Testing conventions
- Add or update feature tests for behavior changes, especially API CRUD and relationship endpoints.
- Reuse factories, permission seeders, and Sanctum authentication patterns from existing API tests.
- Match the current PHPUnit style: descriptive public methods such as `it_stores_the_ticket`.
- Assert on status codes, redirects, JSON fragments, rendered content, and database state.

### Frontend / JS
- Keep JS lightweight and consistent with the existing Vite + Alpine setup.
- Prefer small edits in `resources/js` or Blade/Livewire templates over introducing a new frontend framework.
- Do not introduce TypeScript unless the repo adopts it intentionally across the frontend.

## Agent Checklist
- Run `./vendor/bin/pint` on changed PHP files or relevant directories.
- Run the narrowest useful test first: single method, then file, then broader suite if needed.
- If you changed routes, validation, policies, or relationships, verify matching feature/API coverage.
- Call out any command you could not run and why.
