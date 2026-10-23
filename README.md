# TaskFlow Laravel API

Laravel 12 REST API for TaskFlow. It provides authentication, role-aware project and task operations, and the shared MySQL data layer used by the React client and Django overdue service.

## Features

- Sanctum bearer-token login, registration, logout, and current-user endpoint
- Admin-managed projects and task creation, with authenticated task updates
- Role middleware for administrator-only operations
- MySQL migrations and demo data seeder
- Integration point for the Django overdue-task processor

## Stack

PHP 8.2+, Laravel 12, Laravel Sanctum, MySQL 8+

## Run locally

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`; set `APP_KEY` with `php artisan key:generate`.
3. Create a MySQL database named `taskflow` and set `DB_*` values in `.env`.
4. Run `php artisan migrate --seed`.
5. Start the API with `php artisan serve --host 127.0.0.1 --port 8000`.

Demo accounts created by the seeder:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@taskflow.local` | `Admin123!` |
| Member | `member@taskflow.local` | `Member123!` |

## API overview

All routes are under `/api`. Login and registration are public; the remaining routes require a Sanctum token in `Authorization: Bearer <token>`.

| Method | Path | Access |
| --- | --- | --- |
| `POST` | `/api/login` | Public |
| `POST` | `/api/register` | Public; creates a regular member |
| `POST` | `/api/logout` | Authenticated |
| `GET` | `/api/me` | Authenticated |
| `GET` | `/api/projects` | Authenticated |
| `GET` | `/api/projects/{project}` | Authenticated |
| `POST` | `/api/projects` | Admin |
| `POST` | `/api/projects/{project}/tasks` | Admin |
| `GET` | `/api/users` | Authenticated |
| `PATCH` | `/api/tasks/{task}` | Authenticated |

## Wasmer deployment

`app.yaml.example` and `wasmer.toml` are deployment starting points. Copy the example to `app.yaml`, replace its owner placeholder with your Wasmer username, then configure production secrets in Wasmer: `APP_ENV=production`, `APP_DEBUG=false`, a generated `APP_KEY`, `APP_URL`, `FRONTEND_URL`, and the MySQL connection values (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`). Configure the Django service URL and shared sync token as described in `.env.example`. Review the pre-deploy migration/seed job before the first release; do not expose production credentials in Git.

Live API URL: _To be added after deployment._

## Related repositories

- [TaskFlow React frontend](https://github.com/zagesh7/taskflow-react-frontend)
- [TaskFlow Django overdue service](https://github.com/zagesh7/taskflow-django-overdue)

The system architecture and request/process flow are documented in [`ARCHITECTURE.md`](ARCHITECTURE.md).
