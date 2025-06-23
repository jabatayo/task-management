# Task Management API Documentation

## Overview

The Task Management API is a RESTful service built with Laravel 12 that provides comprehensive task management functionality with role-based access control and enterprise-grade security.

**Base URL**: `http://localhost:8000/api`

## Authentication

All protected endpoints require authentication using Laravel Sanctum tokens.

### Headers

```http
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### 🔐 Authentication

#### Register User

**POST** `/api/register`

Creates a new user account with "Regular User" role by default.

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Validation Rules:**

- `name`: required, string, max 255 characters
- `email`: required, valid email, unique
- `password`: required, string, min 8 characters, confirmed

**Response (201):**

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

**Rate Limit:** 5 requests per minute

---

#### Login User

**POST** `/api/login`

Authenticates a user and returns an access token.

**Request Body:**

```json
{
    "email": "admin@taskmanagement.com",
    "password": "password123"
}
```

**Validation Rules:**

- `email`: required, valid email, must exist in users table
- `password`: required, string

**Response (200):**

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

**Rate Limit:** 5 requests per minute

---

#### Logout User

**POST** `/api/logout`

Revokes the current access token.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**

```json
{
    "message": "Logged out successfully."
}
```

---

### 📋 Tasks

#### List Tasks

**GET** `/api/tasks`

Retrieves a paginated list of tasks with filtering and search capabilities.

**Query Parameters:**

- `status` (optional): Filter by status (`pending`, `in_progress`, `completed`, `cancelled`)
- `priority` (optional): Filter by priority (`low`, `medium`, `high`, `urgent`)
- `assigned_to` (optional): Filter by assigned user ID
- `search` (optional): Global search in title and description
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc`, `desc`, default: `desc`)
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**

```http
GET /api/tasks?status=pending&priority=high&search=documentation&page=1&per_page=15
Authorization: Bearer {token}
```

**Response (200):**

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

**Authorization:** Users can only see tasks they created or are assigned to, unless they have Administrator role.

---

#### Create Task

**POST** `/api/tasks`

Creates a new task.

**Request Body:**

```json
{
    "title": "New Task",
    "description": "Task description",
    "priority": "high",
    "due_date": "2025-06-30",
    "assigned_to": 2,
    "tags": ["urgent", "frontend"]
}
```

**Validation Rules:**

- `title`: required, string, max 255 characters
- `description`: optional, string, max 1000 characters
- `status`: optional, in: `pending`, `in_progress`, `completed`, `cancelled`
- `priority`: optional, in: `low`, `medium`, `high`, `urgent`
- `due_date`: optional, date, after or equal to today
- `assigned_to`: optional, exists in users table
- `tags`: optional, array of strings, max 50 characters each

**Response (201):**

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

---

#### Get Task

**GET** `/api/tasks/{id}`

Retrieves a specific task by ID.

**Response (200):**

```json
{
    "task": {
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
}
```

**Authorization:** Users can only access tasks they created or are assigned to, unless they have Administrator role.

---

#### Update Task

**PUT** `/api/tasks/{id}`

Updates an existing task.

**Request Body:**

```json
{
    "title": "Updated Task Title",
    "status": "completed",
    "priority": "medium"
}
```

**Response (200):**

```json
{
    "message": "Task updated successfully",
    "task": {
        "id": 1,
        "title": "Updated Task Title",
        "description": "Write comprehensive API documentation",
        "status": "completed",
        "priority": "medium",
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
        "updated_at": "2025-06-19 14:15:30"
    }
}
```

**Authorization:** Users can only update tasks they created or are assigned to, unless they have Administrator role.

---

#### Delete Task

**DELETE** `/api/tasks/{id}`

Deletes a task.

**Response (200):**

```json
{
    "message": "Task deleted successfully"
}
```

**Authorization:** Only the task creator or Administrator can delete tasks.

---

### 📊 Dashboard

#### Get Dashboard Metrics

**GET** `/api/dashboard`

Retrieves comprehensive dashboard metrics and analytics.

**Response (200):**

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

**Authorization:** Users see metrics only for their own/assigned tasks, unless they have Administrator role.

---

### 📞 Contact

#### Submit Contact Form

**POST** `/api/contact`

Submits a contact form message.

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "I would like to request a feature for the task management system."
}
```

**Validation Rules:**

- `name`: required, string, max 255 characters
- `email`: required, valid email, max 255 characters
- `message`: required, string, max 2000 characters

**Response (200):**

```json
{
    "message": "Thank you for contacting us! We will get back to you soon."
}
```

**Rate Limit:** 10 requests per minute

---

### ℹ️ About

#### Get About Information

**GET** `/api/about`

Retrieves static information about the application.

**Response (200):**

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
    "repository": "https://github.com/your-repo/task-management",
    "contact_email": "support@taskmanagement.com"
}
```

---

## Error Responses

### Validation Error (422)

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

### Authentication Error (401)

```json
{
    "message": "Unauthenticated."
}
```

### Authorization Error (403)

```json
{
    "message": "Unauthorized"
}
```

### Not Found Error (404)

```json
{
    "message": "Task not found."
}
```

### Rate Limit Error (429)

```json
{
    "message": "Too Many Attempts.",
    "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

### Server Error (500)

```json
{
    "message": "Server Error"
}
```

---

## Data Models

### Task Model

```json
{
    "id": "integer",
    "title": "string (required, max 255)",
    "description": "string (optional, max 1000)",
    "status": "enum (pending, in_progress, completed, cancelled)",
    "priority": "enum (low, medium, high, urgent)",
    "due_date": "date (optional, future date)",
    "assigned_to": "integer (foreign key to users)",
    "created_by": "integer (foreign key to users)",
    "tags": "array (optional)",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### User Model

```json
{
    "id": "integer",
    "name": "string (required, max 255)",
    "email": "string (required, unique, valid email)",
    "password": "string (hashed)",
    "email_verified_at": "datetime (optional)",
    "created_at": "datetime",
    "updated_at": "datetime",
    "roles": "array (relationship)"
}
```

---

## Security Features

### Authentication

- Token-based authentication using Laravel Sanctum
- Automatic token expiration and revocation
- Secure password hashing with bcrypt

### Authorization

- Role-based access control (RBAC)
- Two roles: Administrator and Regular User
- Database-level filtering based on user permissions

### Rate Limiting

- Authentication endpoints: 5 requests per minute
- Contact form: 10 requests per minute
- Configurable per endpoint

### Security Headers

- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Content-Security-Policy: Comprehensive CSP

### Input Validation

- Server-side validation on all endpoints
- SQL injection protection via Eloquent ORM
- XSS protection via multiple layers

---

## Testing

### Example API Tests

```php
// Test user registration
$response = $this->postJson('/api/register', [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
]);

$response->assertStatus(201);

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

---

## Support

For API support and questions:

- Email: support@taskmanagement.com
- Documentation: [Main README](../README.md)
- Issues: [GitHub Issues](https://github.com/your-repo/task-management/issues)
