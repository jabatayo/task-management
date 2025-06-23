# Task Management API

A modern, secure, and robust task management platform built with Laravel 12 and React, featuring role-based access control, comprehensive security measures, and enterprise-grade architecture.

## 🏗️ Architecture Overview

### Technology Stack

- **Backend**: Laravel 12 (PHP 8.3)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (Token-based)
- **Frontend**: React (planned)
- **Security**: Role-based Access Control (RBAC), Rate Limiting, Security Headers

### Key Architectural Decisions

#### 1. **Security-First Approach**

- **Authentication**: Token-based authentication using Laravel Sanctum
- **Authorization**: Role-based access control with two distinct roles (Administrator, Regular User)
- **Input Validation**: Comprehensive server-side validation with custom request classes
- **Rate Limiting**: Configurable rate limiting for API endpoints
- **Security Headers**: Multiple security headers for XSS, clickjacking, and content type protection

#### 2. **Database Design**

- **Normalized Schema**: Proper relationships between users, roles, and tasks
- **Indexing Strategy**: Optimized indexes for performance on large datasets
- **Data Integrity**: Foreign key constraints and proper cascading rules

#### 3. **API Design**

- **RESTful Principles**: Standard HTTP methods and status codes
- **Consistent Responses**: Structured JSON responses with proper error handling
- **Pagination**: Built-in pagination for list endpoints
- **Filtering & Search**: Advanced filtering and search capabilities

#### 4. **Scalability Considerations**

- **Middleware Stack**: Efficient middleware pipeline
- **Query Optimization**: Eloquent scopes and eager loading
- **Caching Ready**: Database-driven cache configuration

## 🚀 Quick Start

### Prerequisites

- PHP 8.3+
- Composer
- MySQL 8.0+
- Node.js (for frontend development)

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/jabatayo/task-management.git
   cd task-management
   ```

2. **Backend Setup**

   ```bash
   cd backend
   composer install
   cp .env.example .env
   ```

3. **Configure Environment**

   ```bash
   # Database Configuration
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   # Application Key
   php artisan key:generate
   ```

4. **Database Setup**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

### Default Users

After seeding, the following users are available:

- **Admin**: `admin@taskmanagement.com` / `password123`
- **Regular User**: `user@taskmanagement.com` / `password123`

## 🔐 Security Implementation

### Authentication & Authorization

#### **Authentication Mechanism**

- **Laravel Sanctum**: Token-based authentication
- **Password Hashing**: Bcrypt with Laravel's Hash facade
- **Token Management**: Automatic token expiration and revocation

#### **Role-Based Access Control (RBAC)**

```php
// User Roles
- Administrator: Full access to all features and data
- Regular User: Limited access to own/assigned tasks
```

#### **Authorization Enforcement**

- **Frontend**: UI restrictions based on user roles
- **Backend**: API endpoint protection with middleware
- **Data Access**: Database-level filtering based on user permissions

### Security Headers

```http
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https:; frame-ancestors 'none';
```

### Rate Limiting

- **Authentication Endpoints**: 5 requests per minute
- **Contact Form**: 10 requests per minute
- **General API**: Configurable per endpoint

### Input Validation

- **Server-side Validation**: Comprehensive validation rules
- **SQL Injection Protection**: Eloquent ORM with parameterized queries
- **XSS Protection**: Multiple layers including CSP headers

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

All protected endpoints require the `Authorization` header:

```http
Authorization: Bearer {your_token}
```

### Endpoints

#### 🔐 Authentication

##### Register User

```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "user": {
        "id": 3,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-06-19T13:31:53.000000Z",
        "updated_at": "2025-06-19T13:31:53.000000Z"
    },
    "token": "1|JpjJrHbvvv8O2wiNMUvkWZeWWI9HJNkCofsdTwWE339472a2"
}
```

##### Login User

```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@taskmanagement.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@taskmanagement.com"
    },
    "token": "2|gTXCgpfwRi1mIpgvykq4xAzVUfEzvszUAm6Iew9l444ae754"
}
```

##### Logout User

```http
POST /api/logout
Authorization: Bearer {token}
```

**Response:**

```json
{
    "message": "Logged out successfully."
}
```

#### 📋 Tasks

##### List Tasks

```http
GET /api/tasks?status=pending&priority=high&search=documentation&page=1&per_page=15
Authorization: Bearer {token}
```

**Query Parameters:**

- `status`: Filter by status (pending, in_progress, completed, cancelled)
- `priority`: Filter by priority (low, medium, high, urgent)
- `assigned_to`: Filter by assigned user ID
- `search`: Global search in title and description
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort direction (asc, desc)
- `page`: Page number for pagination
- `per_page`: Items per page (default: 15)

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Complete API Documentation",
            "description": "Write comprehensive API documentation",
            "status": "in_progress",
            "priority": "high",
            "due_date": "2025-06-25",
            "tags": ["documentation", "api", "priority"],
            "created_by": {
                "id": 1,
                "name": "Admin User",
                "email": "admin@taskmanagement.com"
            },
            "assigned_to": {
                "id": 1,
                "name": "Admin User",
                "email": "admin@taskmanagement.com"
            },
            "created_at": "2025-06-19 13:34:37",
            "updated_at": "2025-06-19 13:34:51"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/tasks?page=1",
        "last": "http://localhost:8000/api/tasks?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

##### Create Task

```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "New Task",
    "description": "Task description",
    "priority": "high",
    "due_date": "2025-06-30",
    "assigned_to": 2,
    "tags": ["urgent", "frontend"]
}
```

**Response:**

```json
{
    "message": "Task created successfully",
    "task": {
        "id": 5,
        "title": "New Task",
        "description": "Task description",
        "status": "pending",
        "priority": "high",
        "due_date": "2025-06-30",
        "tags": ["urgent", "frontend"],
        "created_by": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@taskmanagement.com"
        },
        "assigned_to": {
            "id": 2,
            "name": "Regular User",
            "email": "user@taskmanagement.com"
        },
        "created_at": "2025-06-19 14:00:00",
        "updated_at": "2025-06-19 14:00:00"
    }
}
```

##### Get Task

```http
GET /api/tasks/{id}
Authorization: Bearer {token}
```

##### Update Task

```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Task Title",
    "status": "completed",
    "priority": "medium"
}
```

##### Delete Task

```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

**Response:**

```json
{
    "message": "Task deleted successfully"
}
```

#### 📊 Dashboard

##### Get Dashboard Metrics

```http
GET /api/dashboard
Authorization: Bearer {token}
```

**Response:**

```json
{
    "task_statistics": {
        "total_tasks": 4,
        "completed_tasks": 0,
        "pending_tasks": 3,
        "in_progress_tasks": 1,
        "cancelled_tasks": 0,
        "completion_rate": 0
    },
    "recent_activity": [
        {
            "id": 4,
            "title": "Database Optimization",
            "status": "pending",
            "priority": "low",
            "updated_at": "2025-06-19 13:42:56",
            "creator": {
                "id": 1,
                "name": "Admin User"
            },
            "assignee": {
                "id": 1,
                "name": "Admin User"
            }
        }
    ],
    "performance_metrics": {
        "tasks_created_this_month": 4,
        "tasks_completed_this_month": 0,
        "completion_rate_this_month": 0,
        "average_completion_time_days": 0
    },
    "priority_distribution": {
        "low": 1,
        "medium": 1,
        "high": 1,
        "urgent": 1
    },
    "status_distribution": {
        "pending": 3,
        "in_progress": 1,
        "completed": 0,
        "cancelled": 0
    },
    "overdue_tasks": [],
    "upcoming_deadlines": [
        {
            "id": 3,
            "title": "Frontend Development",
            "priority": "urgent",
            "due_date": "2025-06-20",
            "days_until_due": 1,
            "assignee": {
                "id": 1,
                "name": "Admin User"
            }
        }
    ]
}
```

#### 📞 Contact

##### Submit Contact Form

```http
POST /api/contact
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "I would like to request a feature for the task management system."
}
```

**Response:**

```json
{
    "message": "Thank you for contacting us! We will get back to you soon."
}
```

#### ℹ️ About

##### Get About Information

```http
GET /api/about
```

**Response:**

```json
{
    "app_name": "Task Management System",
    "version": "1.0.0",
    "description": "A modern, secure, and robust task management platform built with Laravel and React.",
    "team": [
        {
            "name": "Jonathan",
            "role": "Lead Developer"
        }
    ],
    "repository": "https://github.com/jabatayo/task-management",
    "contact_email": "jabatayo@gmail.com"
}
```

### Error Responses

#### Validation Error

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

#### Authentication Error

```json
{
    "message": "Unauthenticated."
}
```

#### Authorization Error

```json
{
    "message": "Unauthorized"
}
```

#### Rate Limit Error

```json
{
    "message": "Too Many Attempts.",
    "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

## 🧪 Testing Strategy

### Types of Tests

1. **Unit Tests**: Individual components and methods
2. **Feature Tests**: API endpoints and business logic
3. **Integration Tests**: Database interactions and external services
4. **Security Tests**: Authentication, authorization, and input validation

### Testing Tools

- **PHPUnit**: Laravel's built-in testing framework
- **Laravel Sanctum**: Authentication testing utilities
- **Database Factories**: Test data generation

### Example Test

```php
public function test_user_can_create_task()
{
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/tasks', [
        'title' => 'Test Task',
        'description' => 'Test Description',
        'priority' => 'high'
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'message',
                 'task' => [
                     'id', 'title', 'description', 'priority'
                 ]
             ]);
}
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up SSL/TLS certificates
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up monitoring and logging
- [ ] Configure backup strategy
- [ ] Set up CI/CD pipeline

### Docker Support

```dockerfile
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
```

## 📈 Performance & Monitoring

### Performance Optimizations

- **Database Indexing**: Optimized indexes for common queries
- **Eager Loading**: Prevents N+1 query problems
- **Caching**: Database-driven cache configuration
- **Rate Limiting**: Prevents API abuse

### Monitoring

- **Application Logs**: Structured logging for debugging
- **Security Events**: Authentication and validation failures
- **Performance Metrics**: Response times and database queries
- **Error Tracking**: Exception monitoring and alerting

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:

- Email: jabatayo@gmail.com
- Documentation: [API Docs](docs/api.md)
- Issues: [GitHub Issues](https://github.com/jabatayo/task-management/issues)
