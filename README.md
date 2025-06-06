# Employee Management API

A RESTful API for managing employees with address information, built with Laravel and Laravel Sanctum for authentication.

## Features

- Employee CRUD operations with address management
- Authentication with API tokens (Laravel Sanctum)
- Filtering, sorting, and pagination
- Comprehensive validation
- Automatic API documentation with Scribe
- Full test coverage

## Requirements

- Docker & Docker Compose (recommended via Laravel Sail)
- PHP 8.2+
- MySQL 8.0+
- Composer

## Installation & Setup

### 1. Clone and Setup

```bash
git clone <repository-url>
cd primeo
cp .env.example .env
```

### 2. Configure Environment

Edit `.env` file and set database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=primeo
DB_USERNAME=sail
DB_PASSWORD=password
```

### 3. Install Dependencies & Start

```bash
# Install Composer dependencies
composer install

# Start Docker containers
./vendor/bin/sail up -d

# Generate application key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database with test data
./vendor/bin/sail artisan db:seed
```

### 4. Generate API Documentation

```bash
./vendor/bin/sail artisan scribe:generate
```

## API Documentation

After running `scribe:generate`, documentation will be available at:
- **URL**: `http://localhost/docs`

## Test Accounts

The database seeder creates two test accounts:

### Active Employee Account
- **Email**: `active@example.com`
- **Password**: `password123`
- **Status**: Active (can login)

### Inactive Employee Account  
- **Email**: `inactive@example.com`
- **Password**: `password123`
- **Status**: Inactive (login blocked)

## API Endpoints

### Public Endpoints

#### List Employees
```
GET /api/employees
```

**Query Parameters:**
- `filter[full_name]` - Filter by full name
- `filter[email]` - Filter by email
- `filter[position]` - Filter by position (front-end, back-end, pm, designer, tester)
- `filter[is_active]` - Filter by active status (true/false)
- `sort` - Sort by field (full_name, email, position, created_at). Use `-` prefix for descending
- `page[number]` - Page number
- `page[size]` - Items per page (max 100)

**Example:**
```bash
curl "http://localhost/api/employees?filter[position]=front-end&sort=-created_at&page[size]=5"
```

### Authentication Endpoints

#### Login
```
POST /api/auth/login
```

**Request Body:**
```json
{
  "email": "active@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "1|abcdef...",
  "employee": { ... }
}
```

### Protected Endpoints (Require Authentication)

Add `Authorization: Bearer {token}` header to all protected endpoints.

#### Create Employee
```
POST /api/employees
```

**Request Body:**
```json
{
  "full_name": "John Doe",
  "email": "john.doe@example.com",
  "phone": "+48123456789",
  "average_annual_salary": 75000.50,
  "position": "front-end",
  "password": "password123",
  "residential_address_country": "Poland",
  "residential_address_postal_code": "00-123",
  "residential_address_city": "Warsaw",
  "residential_address_house_number": "15A",
  "residential_address_apartment_number": "5",
  "different_correspondence_address": true,
  "correspondence_address_country": "Germany",
  "correspondence_address_postal_code": "10115",
  "correspondence_address_city": "Berlin",
  "correspondence_address_house_number": "22",
  "correspondence_address_apartment_number": "10",
  "is_active": true
}
```

#### Update Employee
```
PUT /api/employees/{id}
```

**Request Body (all fields optional):**
```json
{
  "full_name": "John Doe Updated",
  "position": "back-end",
  "different_correspondence_address": false
}
```

#### Get Current User Info
```
GET /api/me
```

## Testing with Postman

### 1. Setup Environment

Create a new Postman environment with:
- `base_url`: `http://localhost/api`
- `token`: (will be set after login)

### 2. Login to Get Token

**Request:**
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
  "email": "active@example.com",
  "password": "password123"
}
```

**After Login:**
1. Copy the `token` from response
2. Set it in your Postman environment as `token` variable

### 3. Test Protected Endpoints

Add to Headers for all protected requests:
```
Authorization: Bearer {{token}}
```

### 4. Complete Test Scenarios

#### Scenario 1: List and Filter Employees
```bash
# Get all employees
GET {{base_url}}/employees

# Filter by position
GET {{base_url}}/employees?filter[position]=front-end

# Sort by name with pagination
GET {{base_url}}/employees?sort=full_name&page[size]=5&page[number]=1
```

#### Scenario 2: Create New Employee
```bash
# Create employee with same addresses
POST {{base_url}}/employees
Authorization: Bearer {{token}}

{
  "full_name": "Jane Smith",
  "email": "jane.smith@example.com",
  "position": "designer",
  "password": "password123",
  "residential_address_country": "Poland",
  "residential_address_postal_code": "00-001",
  "residential_address_city": "Krakow",
  "residential_address_house_number": "10",
  "different_correspondence_address": false,
  "is_active": true
}
```

#### Scenario 3: Update Employee
```bash
# Partial update
PUT {{base_url}}/employees/1
Authorization: Bearer {{token}}

{
  "position": "back-end",
  "average_annual_salary": 85000
}
```

#### Scenario 4: Test Validation
```bash
# Try creating employee with invalid data
POST {{base_url}}/employees
Authorization: Bearer {{token}}

{
  "email": "invalid-email",
  "position": "invalid-position"
}
```

## Running Tests

The project includes comprehensive automated tests and Postman collections for API testing.

### 1. Automated PHPUnit Tests

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/AuthLoginTest.php

# Run only feature tests (API endpoints)
./vendor/bin/sail artisan test --testsuite=Feature

# Run with verbose output
./vendor/bin/sail artisan test -v

# Run specific test method
./vendor/bin/sail artisan test --filter="it_can_login_with_valid_credentials"

# Run with test coverage (requires Xdebug)
./vendor/bin/sail artisan test --coverage
```

**Test Coverage Summary:**
- **AuthLoginTest** (11 tests) - Login authentication, validation, token generation
- **EmployeeListTest** (11 tests) - Public listing, filtering, sorting, pagination  
- **EmployeeStoreTest** (12 tests) - Employee creation, address handling, validation
- **EmployeeUpdateTest** (16 tests) - Employee updates, partial updates, validation
- **Total: 50+ tests with 280+ assertions**

### 2. Postman API Tests

#### Import Collections

1. **Import Collection:**
   - Open Postman
   - Click "Import" → "Upload Files"
   - Select `postman/Employee_Management_API.postman_collection.json`

2. **Import Environment:**
   - Click "Import" → "Upload Files" 
   - Select `postman/Employee_Management_Environment.postman_environment.json`
   - Set as active environment (top-right dropdown)

#### Running Postman Tests

**Option A: Run Individual Requests**
1. Start with "Authentication" → "Login - Active Employee"
2. This sets the `token` variable automatically
3. Run other protected endpoints

**Option B: Run Collection**
1. Right-click collection → "Run collection"
2. Select all folders
3. Click "Run Employee Management API"
4. View test results and assertions

**Option C: Command Line (Newman)**
```bash
# Install Newman globally
npm install -g newman

# Run collection from command line
newman run postman/Employee_Management_API.postman_collection.json \
  -e postman/Employee_Management_Environment.postman_environment.json \
  --reporters cli,html \
  --reporter-html-export newman-report.html
```

#### Test Scenarios Included

**Authentication Tests:**
- ✅ Login with active employee
- ❌ Login with inactive employee (should fail)
- ❌ Login with invalid credentials
- ❌ Validation errors for malformed data

**Public Endpoint Tests:**
- ✅ Get all employees
- ✅ Filter by position
- ✅ Filter by active status
- ✅ Sort by name
- ✅ Pagination functionality

**Protected Endpoint Tests:**
- ✅ Create employee (same addresses)
- ✅ Create employee (different addresses)
- ❌ Create employee validation errors
- ✅ Update employee (partial)
- ✅ Update employee addresses
- ❌ Update non-existent employee
- ✅ Get current user info
- ❌ Access without authentication

### 3. Manual Testing Quick Start

**Step 1: Get Authentication Token**
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "active@example.com", "password": "password123"}'
```

**Step 2: Test Protected Endpoints**
```bash
# Replace YOUR_TOKEN with token from step 1
curl -X GET http://localhost/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Test Database Setup

```bash
# Reset database and seed test data
./vendor/bin/sail artisan migrate:fresh --seed

# This creates:
# - active@example.com (can login)
# - inactive@example.com (login blocked)  
# - 15 additional random employees for testing
```

### 5. Continuous Integration

```bash
# Run all quality checks
./vendor/bin/sail composer ecs          # Code style check
./vendor/bin/sail artisan test          # Automated tests
./vendor/bin/sail artisan scribe:generate  # Update API docs
```

### Troubleshooting Tests

**Database Issues:**
```bash
# Reset test database
./vendor/bin/sail artisan migrate:fresh --seed --env=testing
```

**Token Expiration:**
```bash
# Re-run login request in Postman to get fresh token
# Or clear token from environment and re-authenticate
```

**Port Conflicts:**
```bash
# Check if application is running
./vendor/bin/sail ps
# Restart if needed
./vendor/bin/sail down && ./vendor/bin/sail up -d
```

## Code Quality

```bash
# Check code style
./vendor/bin/sail composer ecs

# Fix code style
./vendor/bin/sail composer ecs-fix
```

## Development Commands

```bash
# Start development server
./vendor/bin/sail artisan serve

# Clear all caches
./vendor/bin/sail artisan optimize:clear

# Refresh database with test data
./vendor/bin/sail artisan migrate:fresh --seed

# Generate new API documentation
./vendor/bin/sail artisan scribe:generate
```

## Troubleshooting

### Database Connection Issues
```bash
# Check if containers are running
./vendor/bin/sail ps

# Restart containers
./vendor/bin/sail down && ./vendor/bin/sail up -d
```

### Permission Issues
```bash
# Fix storage permissions
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Clear All Caches
```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
```

## Architecture

- **Framework**: Laravel 12.x
- **Authentication**: Laravel Sanctum (API tokens)
- **Database**: MySQL with Eloquent ORM
- **Validation**: Form Request classes
- **Documentation**: Scribe
- **Testing**: PHPUnit with Feature & Unit tests
- **Code Style**: Easy Coding Standard (ECS)
- **Query Builder**: Spatie Laravel Query Builder
- **Pagination**: Spatie Laravel JSON API Paginate

## License

This code is provided for **demonstration and testing purposes only**. 

**Permitted uses:**
- View and examine the code
- Run and test the application locally
- Use for educational purposes

**Prohibited uses:**
- Use in commercial projects
- Redistribute or share the code
- Modify and use in your own projects
- Create derivative works

**All rights reserved.** This software is provided without any license for commercial or production use.