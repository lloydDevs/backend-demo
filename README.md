# Laravel Modular Boilerplate

A Laravel + Inertia (Vue 3) starter built around a **manual modular architecture** —
no `php artisan module:make`, no third-party module package. Every feature lives in
its own self-contained folder under `app/Modules/`, following a Repository → Service →
Controller pattern, with role/permission-based access control baked in from the start.

Includes a fully working sample CRUD module (`Product`) so you can see the pattern in
action immediately, plus a Pest test suite proving auth, permissions, validation, and
error handling all actually work — not just scaffolded.

## Stack

- **Laravel 13**
- **Laravel Fortify** — authentication backend (login, register, password reset)
- **Inertia.js (Vue 3)** — server-driven SPA, no separate API layer
- **Spatie Laravel-Permission** — role-based access control (RBAC)
- **Tailwind CSS 4**
- **Pest** — test suite

## Architecture at a glance

```
app/Modules/
├── Core/       Shared base classes (BaseRepository, BaseService, BaseController)
├── Product/    Full working sample module — copy this to build your own
├── Auth/       Fortify wiring (custom actions, Inertia auth views)
└── Role/       Roles & permissions seeder
```

Each module owns its own routes, migrations, and a `*ModuleServiceProvider` that
registers everything — new modules are added by copying `Product`'s shape and adding
one line to `bootstrap/providers.php`. No shared central files to edit.

Full architecture write-up, request-flow diagram, and "how to add your next module"
guide: see **[SETUP.md](SETUP.md)**.

## Quick start

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed

npm run dev        # keep running in a separate terminal while developing
php artisan serve
```

Visit `http://localhost:8000` and log in with the seeded demo account:

```
demo@example.com / password
```

This account has the `admin` role, which has full CRUD access to the sample
Product module (`/products`).

> **Heads up:** `npm run dev` watches for file changes automatically. If you only run
> `npm run build` (a one-time compile), you need to re-run it any time you add a new
> Vue page — otherwise Inertia will throw "Page not found" for pages that exist on
> disk but aren't in the last compiled bundle.

## Roles & permissions

Seeded by `RolePermissionSeeder` (`database/seeders/DatabaseSeeder.php` → `RolePermissionSeeder`):

| Role     | Permissions                                                             |
|----------|--------------------------------------------------------------------------|
| `admin`  | `products.view`, `products.create`, `products.edit`, `products.delete`   |
| `editor` | `products.view`, `products.create`, `products.edit`                      |
| `viewer` | `products.view`                                                          |

New users who self-register via Fortify are assigned `viewer` by default
(`app/Modules/Auth/Actions/Fortify/CreateNewUser.php`).

Enforcement happens at three layers, and only one of them actually matters for
security — the other two are UX:

1. **Policy + controller** (`ProductPolicy` + `$this->authorize()` in `ProductController`)
   — the real security boundary
2. **Form request** (`StoreProductRequest`/`UpdateProductRequest::authorize()`) —
   redundant check before validation runs
3. **Frontend** (`auth.permissions` shared via `HandleInertiaRequests`, read in
   `Product/Index.vue`) — hides buttons the user can't use; cosmetic only

## Testing

```bash
php artisan test
```

Runs against an in-memory SQLite database (`RefreshDatabase`) — never touches your
real dev database. Covers:

- Full Product CRUD round trip + validation rules + permission enforcement per role
- Fortify register/login/logout, including default role assignment
- Role/permission seeder correctness and idempotency
- Custom 403/404 error pages rendering correctly

See [SETUP.md](SETUP.md#running-tests) for the shared test helpers (`userWithRole()`,
`seedRolesAndPermissions()`) available to any new test file.

## Error pages

403 / 404 / 419 / 429 / 500 / 503 all render a single shared Vue page
(`resources/js/Pages/Error.vue`) instead of Laravel's default HTML error pages, so a
denied permission or missing record looks like the rest of the app. In local
development with `APP_DEBUG=true`, 500/503 still fall through to Laravel's detailed
stack-trace page; 403/404/419/429 always show the styled page regardless of debug mode,
since those aren't bugs to debug.

## Adding your next module

1. Copy `app/Modules/Product` → `app/Modules/YourModule`, rename `Product*` throughout
2. Update the migration, model, form requests, and Vue pages
   (`resources/js/Pages/YourModule/{Index,Create,Edit,Show}.vue`)
3. Add `YourModuleServiceProvider::class` to `bootstrap/providers.php`
4. Add its permissions to `RolePermissionSeeder` and assign them to the roles that need them
5. Copy `tests/Feature/ProductCrudTest.php` as your test template

Full walkthrough in [SETUP.md](SETUP.md#adding-your-next-module-eg-order).

## Known limitations

- Only one domain module (`Product`) currently exists — the pattern is proven for
  it, not yet stress-tested by a second/third module
- No CI configured yet (tests must be run manually)
- Email verification is disabled (`Features::emailVerification()` commented out in
  `config/fortify.php`); password reset emails need real mail config
  (`MAIL_MAILER` defaults to `array`, i.e. nowhere) before that flow works end-to-end
- No admin UI yet for managing users or roles/permissions — currently seeder-only

## License

[MIT](LICENSE) — or update this section to match your project.