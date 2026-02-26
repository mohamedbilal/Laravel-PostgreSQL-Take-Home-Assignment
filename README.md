# Task Management API (Laravel + PostgreSQL)

A RESTful Task Management API built with Laravel 11 (LTS) and PostgreSQL. Users can register, log in, and manage projects and tasks. Each user owns their projects; each project has many tasks.

## Requirements

- PHP 8.2+
- Composer
- PostgreSQL
- Laravel 11.x

## Setup

1. **Clone and install dependencies**

   ```bash
   composer install
   ```

2. **Environment**

   Copy `.env.example` to `.env` and configure:

   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=laravel
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

   Generate the application key:

   ```bash
   php artisan key:generate
   ```

3. **Migrations**

   ```bash
   php artisan migrate
   ```

4. **Run the application**

   ```bash
   php artisan serve
   ```

   The API will be available at `http://localhost:8000`.

## Running tests

```bash
php artisan test
```

Or with PHPUnit:

```bash
./vendor/bin/phpunit
```

Tests use an in-memory SQLite database by default (see `phpunit.xml`). For PostgreSQL-specific behaviour (e.g. CHECK constraint), run migrations against a PostgreSQL test database and set `DB_CONNECTION=pgsql` in `.env.testing` if you use one.

## API Endpoints

### Authentication

| Method | Endpoint       | Description        |
|--------|----------------|--------------------|
| POST   | `/api/register` | Register a user    |
| POST   | `/api/login`    | Login              |
| POST   | `/api/logout`   | Logout (auth required) |

**Register** – `POST /api/register`

- Body: `name`, `email`, `password`, `password_confirmation`
- Returns: `user`, `token`, `token_type` (201)

**Login** – `POST /api/login`

- Body: `email`, `password`
- Returns: `user`, `token`, `token_type` (200) or 422/401

**Protected routes** – Send header: `Authorization: Bearer {token}`

### Projects (CRUD)

| Method | Endpoint              | Description        |
|--------|------------------------|--------------------|
| GET    | `/api/projects`        | List user's projects |
| POST   | `/api/projects`       | Create project     |
| GET    | `/api/projects/{id}`  | Show project       |
| PUT    | `/api/projects/{id}`  | Update project     |
| DELETE | `/api/projects/{id}`  | Delete project     |

### Tasks

| Method | Endpoint                              | Description   |
|--------|---------------------------------------|----------------|
| POST   | `/api/projects/{project}/tasks`       | Create task   |
| PUT    | `/api/projects/{project}/tasks/{task}`| Update task   |
| DELETE | `/api/projects/{project}/tasks/{task}`| Delete task   |

Task `status` must be one of: `pending`, `in_progress`, `completed`.  
Tasks support an optional `metadata` JSONB column for extra data.

## Assumptions

- Only the project owner can view, update, or delete a project and its tasks.
- Access to another user’s project or task returns **404** (treated as “not found”).
- Task `status` is validated in the API and enforced in PostgreSQL with a CHECK constraint.
- Passwords are hashed with bcrypt; API tokens are issued via Laravel Sanctum.
- No pagination on list endpoints; add `?page=` and use `LengthAwarePaginator` if needed later.

## PostgreSQL-specific features

- **CHECK constraint** on `tasks.status`: only `pending`, `in_progress`, `completed` are allowed.
- **JSONB column** `tasks.metadata` for flexible key-value metadata on tasks.

## License

MIT.
