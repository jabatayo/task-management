# Quick Reference Guide

## 🚀 Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- MySQL 8.0+
- Node.js (for frontend)

### Quick Setup

```bash
# Clone and setup
git clone <repository>
cd task-management
./setup.sh

# Or manual setup
cd backend
composer install
cp .env.example .env
# Configure .env with database credentials
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Default Users

- **Admin**: `admin@taskmanagement.com` / `password123`
- **User**: `user@taskmanagement.com` / `password123`

## 📋 Essential Commands

### Laravel Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generate application key
php artisan key:generate

# Create new controller
php artisan make:controller Api/NewController

# Create new model with migration
php artisan make:model NewModel -m

# Create new request class
php artisan make:request NewRequest
```

### Database Commands

```bash
# Reset database
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

## 🔐 Authentication

### Get Token

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@taskmanagement.com",
    "password": "password123"
  }'
```

### Use Token

```bash
# All protected endpoints
curl -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  http://localhost:8000/api/tasks
```

## 📚 API Endpoints

### Authentication

- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user

### Tasks

- `GET /api/tasks` - List tasks (with filtering)
- `POST /api/tasks` - Create task
- `GET /api/tasks/{id}` - Get task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task

### Dashboard

- `GET /api/dashboard` - Get dashboard metrics

### Contact & About

- `POST /api/contact` - Submit contact form
- `GET /api/about` - Get about information

## 🔍 Query Parameters

### Task Filtering

```
GET /api/tasks?status=pending&priority=high&search=documentation&page=1&per_page=15
```

**Available filters:**

- `status`: pending, in_progress, completed, cancelled
- `priority`: low, medium, high, urgent
- `assigned_to`: user ID
- `search`: global search in title/description
- `sort_by`: field name (default: created_at)
- `sort_order`: asc, desc (default: desc)
- `page`: page number (default: 1)
- `per_page`: items per page (default: 15, max: 100)

## 🛡️ Security Features

### Rate Limiting

- Authentication: 5 requests/minute
- Contact form: 10 requests/minute
- General API: configurable

### Security Headers

- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy: Comprehensive CSP

### Authorization

- **Administrator**: Full access to all data
- **Regular User**: Access only to own/assigned tasks

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TaskTest.php

# Run with coverage
php artisan test --coverage
```

### Example Test

```php
// Test task creation
$user = User::factory()->create();
$token = $user->createToken('test-token')->plainTextToken;

$response = $this->withHeaders([
    'Authorization' => 'Bearer ' . $token,
])->postJson('/api/tasks', [
    'title' => 'Test Task',
    'description' => 'Test Description',
    'priority' => 'high'
]);

$response->assertStatus(201);
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Database Connection

```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo();
```

#### 2. Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Composer Issues

```bash
# Update composer
composer self-update
composer install --no-cache
```

### Error Codes

| Code | Meaning          | Solution                           |
| ---- | ---------------- | ---------------------------------- |
| 401  | Unauthenticated  | Include valid Authorization header |
| 403  | Unauthorized     | Check user permissions/role        |
| 422  | Validation Error | Check request data format          |
| 429  | Rate Limited     | Wait before retrying               |
| 500  | Server Error     | Check logs in `storage/logs/`      |

## 📊 Database Schema

### Users Table

```sql
users:
- id (primary key)
- name (string)
- email (string, unique)
- password (hashed)
- email_verified_at (datetime)
- created_at, updated_at
```

### Tasks Table

```sql
tasks:
- id (primary key)
- title (string)
- description (text)
- status (enum)
- priority (enum)
- due_date (date)
- assigned_to (foreign key)
- created_by (foreign key)
- tags (json)
- created_at, updated_at
```

### Roles Table

```sql
roles:
- id (primary key)
- name (string)
- created_at, updated_at
```

## 🔧 Configuration

### Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Application
APP_NAME="Task Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Security
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Rate Limiting Configuration

```php
// In routes/api.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 requests per minute
```

## 📈 Performance Tips

### Database Optimization

```php
// Use eager loading to prevent N+1 queries
$tasks = Task::with(['createdBy', 'assignedTo'])->get();

// Use database indexes
// Add indexes to frequently queried columns
```

### Caching

```php
// Cache expensive queries
$metrics = Cache::remember('dashboard_metrics', 300, function () {
    return DashboardController::getMetrics();
});
```

## 🚀 Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Configure production database
- [ ] Set up SSL/TLS certificates
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up monitoring and logging
- [ ] Configure backup strategy
- [ ] Set up CI/CD pipeline
- [ ] Test all endpoints
- [ ] Verify security headers
- [ ] Check rate limiting

## 📞 Support

- **Documentation**: [README.md](../README.md)
- **API Docs**: [docs/api.md](api.md)
- **Postman Collection**: [docs/postman-collection.json](postman-collection.json)
- **Issues**: GitHub Issues
- **Email**: support@taskmanagement.com
