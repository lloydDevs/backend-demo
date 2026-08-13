# Laravel Modular Boilerplate — Setup

This project was assembled by hand-writing files directly (no `php artisan module:make`,
no sandbox PHP available to run composer here) — you'll run the install commands
below on your own machine.

## 1. Install dependencies

```bash
composer install
npm install
```

This pulls in the three new packages declared in `composer.json`:
- `laravel/fortify` — auth backend (login/register/password reset)
- `inertiajs/inertia-laravel` — server adapter for Vue pages
- `spatie/laravel-permission` — roles & permissions

and in `package.json`:
- `vue`, `@inertiajs/vue3`, `@vitejs/plugin-vue`

## 2. Env & app key

```bash
cp .env.example .env   # if not already present
php artisan key:generate
```

`.env` already points at the bundled `database/database.sqlite`. Keep that or switch
to MySQL/Postgres in `.env` — nothing else needs to change either way.

## 3. Migrate & seed

```bash
php artisan migrate:fresh --seed
```

This runs:
- Laravel's default migrations (users, cache, jobs)
- `database/migrations/2026_01_01_000001_create_permission_tables.php` (Spatie roles/permissions tables)
- `app/Modules/Product/Database/Migrations/2026_01_01_000000_create_products_table.php`

...then seeds roles/permissions/demo users and 15 sample products
(`database/seeders/DatabaseSeeder.php` → `RolePermissionSeeder` + `ProductFactory`).

**Demo login:** `demo@example.com` / `password` (assigned the `admin` role — full CRUD access)

## 4. Build assets & run

```bash
npm run dev
# in another terminal
php artisan serve
```

Visit `http://localhost:8000`, log in with the demo account, and go to **Products**
to see the working CRUD.

---

## How the "manual modular" structure works

Everything module-specific lives under `app/Modules/<ModuleName>/`, mirroring a
Controller → Service → Repository pattern per module:

```
app/Modules/
├── Core/                          Shared base classes every module extends
│   ├── Repositories/BaseRepository(Interface).php
│   ├── Services/BaseService.php
│   └── Http/Controllers/BaseController.php
│
├── Product/                       Full working sample module
│   ├── Models/Product.php
│   ├── Repositories/ProductRepository(Interface).php
│   ├── Services/ProductService.php
│   ├── Http/Controllers/ProductController.php
│   ├── Http/Requests/Store|UpdateProductRequest.php
│   ├── Policies/ProductPolicy.php
│   ├── Database/Migrations/..._create_products_table.php
│   ├── Database/Factories/ProductFactory.php
│   ├── routes.php                 Module owns its own routes
│   └── Providers/ProductModuleServiceProvider.php   <- registers everything above
│
├── Auth/                          Fortify wiring
│   ├── Actions/Fortify/*.php      Create user / update profile / update & reset password
│   └── Providers/AuthModuleServiceProvider.php
│
└── Role/                          Roles & permissions
    ├── Database/Seeders/RolePermissionSeeder.php
    └── Providers/RoleModuleServiceProvider.php
```

Each module's `Provider` is the single entry point that:
1. Binds its `RepositoryInterface` → concrete `Repository` (constructor injection into the Service)
2. Loads its own `routes.php`
3. Loads its own migrations folder
4. Registers its policy with `Gate::policy(...)`

All module providers are registered once in `bootstrap/providers.php`. **To add a new
module**, copy the `Product` folder as a template, rename things, and add one line to
`bootstrap/providers.php` — no artisan command needed.

Request flow for the sample module:
`routes.php` → `ProductController` (validates via `StoreProductRequest`/`UpdateProductRequest`,
checks `ProductPolicy` via `$this->authorize()`) → `ProductService` (business logic, e.g. stamping
`created_by`) → `ProductRepositoryInterface` → `ProductRepository` (Eloquent queries) → `Product` model.

## Vue folder structure

```
resources/js/
├── Pages/
│   ├── Dashboard.vue
│   ├── Auth/Login.vue, Register.vue, ForgotPassword.vue, ResetPassword.vue
│   └── Product/
│       ├── Index.vue     paginated list, permission-gated actions
│       ├── Create.vue
│       ├── Edit.vue
│       └── Show.vue
├── Components/ProductForm.vue     shared fields used by Create & Edit
└── Layouts/AuthenticatedLayout.vue
```

Every future module's pages should follow the same `Pages/<Module>/{Index,Create,Edit,Show}.vue`
convention.

## Roles & permissions

Seeded by `RolePermissionSeeder`:

| Role   | Permissions                                              |
|--------|------------------------------------------------------------|
| admin  | products.view, products.create, products.edit, products.delete |
| editor | products.view, products.create, products.edit              |
| viewer | products.view                                               |

- Enforced server-side in `ProductPolicy` + `$this->authorize()` calls in `ProductController`,
  and in the form requests' `authorize()` methods.
- Shared to the frontend via `HandleInertiaRequests::share()` as `auth.permissions`, and used
  in `Product/Index.vue` / `Product/Show.vue` to show/hide Edit/Delete buttons.
- New users who self-register via Fortify get the `viewer` role by default
  (`app/Modules/Auth/Actions/Fortify/CreateNewUser.php`).

## Adding your next module (e.g. "Order")

1. Copy `app/Modules/Product` → `app/Modules/Order`, rename `Product*` → `Order*` throughout.
2. Update the migration, model fields, form requests, and Vue pages under `resources/js/Pages/Order/`.
3. Add `OrderModuleServiceProvider::class` to `bootstrap/providers.php`.
4. Add permissions for it to `RolePermissionSeeder` and assign to the roles that need them.

No `php artisan module:make` involved — it's the same manual pattern throughout.
