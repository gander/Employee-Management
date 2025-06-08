# Employee Management

RESTful API for managing employees with Laravel and Laravel Sanctum authentication.

![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/gander/Employee-Management/laravel.yml?branch=master&style=flat&logo=laravel&logoColor=white&label=test)
[![CC BY-NC-SA 4.0][cc-by-nc-sa-shield]][cc-by-nc-sa]

## Quick Start

### 1. Setup
```bash
git clone <repository-url>
cd primeo
composer install
```

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan scribe:generate
```

### 2. Test Accounts
- **Active**: `active@example.com` / `password123` (can login)
- **Inactive**: `inactive@example.com` / `password123` (login blocked)

### 3. API Documentation
- Interactive docs: `http://localhost/docs`
- Base URL: `http://localhost/api`

## Testing with PHPUnit

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/AuthLoginTest.php

# Run with coverage
./vendor/bin/sail artisan test --coverage
```

## Testing with Postman

### 1. Import Collections
1. Import `postman/Employee_Management_API.postman_collection.json`
2. Import `postman/Employee_Management_Environment.postman_environment.json`
3. Set environment as active

### 2. Run Tests
- **Individual**: Start with "Login - Active Employee" to set token
- **Collection**: Right-click → "Run collection"

## Manual Testing

### 1. Get Token
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "active@example.com", "password": "password123"}'
```

### 2. Test Endpoints
```bash
# Public endpoint
curl "http://localhost/api/employees"

# Protected endpoint (replace TOKEN)
curl -X GET http://localhost/api/me \
  -H "Authorization: Bearer TOKEN"
```

## Key Endpoints

### Public
- `GET /api/employees` - List employees (with filtering, sorting, pagination)

### Authentication
- `POST /api/auth/login` - Login
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password

### Protected (require Bearer token)
- `POST /api/employees` - Create employee
- `GET /api/employees/{id}` - Get employee details
- `PUT /api/employees/{id}` - Update employee
- `DELETE /api/employees/{id}` - Delete employee
- `DELETE /api/employees/bulk` - Bulk delete
- `GET /api/me` - Current user info

## Development Commands

```bash
# Reset database with test data
./vendor/bin/sail artisan migrate:fresh --seed

# Check code style
./vendor/bin/sail composer ecs

# Generate API docs
./vendor/bin/sail artisan scribe:generate

# View logs
./vendor/bin/sail artisan tail
```

## Troubleshooting

```bash
# Restart containers
./vendor/bin/sail down && ./vendor/bin/sail up -d

# Clear caches
./vendor/bin/sail artisan optimize:clear

# Check container status
./vendor/bin/sail ps
```

## License

All content in this repository is licensed under a
[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/).

[![CC BY-NC-SA 4.0][cc-by-nc-sa-image]][cc-by-nc-sa]

[cc-by-nc-sa]: http://creativecommons.org/licenses/by-nc-sa/4.0/
[cc-by-nc-sa-image]: https://licensebuttons.net/l/by-nc-sa/4.0/88x31.png
[cc-by-nc-sa-shield]: https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg

## Architecture

- **Framework**: Laravel 12.x
- **Authentication**: Laravel Sanctum (API tokens)
- **Database**: MySQL with Eloquent ORM
- **Documentation**: Scribe
- **Testing**: PHPUnit + Postman
- **Code Style**: Easy Coding Standard (ECS)
