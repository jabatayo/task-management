# API Integration Guide

This guide covers how to integrate with the backend API in the Task Management frontend.

## 🏗️ Architecture Overview

The frontend uses a centralized API service pattern with:

- **Axios** for HTTP requests
- **TypeScript** for type safety
- **Custom hooks** for data fetching
- **Error handling** with user-friendly messages
- **Loading states** for better UX

## 📡 API Service Structure

### Base Configuration (`src/services/api.ts`)

```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor for authentication
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

## 🔧 API Methods

### Authentication

```typescript
// Login
const login = async (credentials: LoginCredentials): Promise<AuthResponse> => {
  const response = await api.post('/auth/login', credentials);
  return response.data;
};

// Register
const register = async (userData: RegisterData): Promise<AuthResponse> => {
  const response = await api.post('/auth/register', userData);
  return response.data;
};

// Logout
const logout = async (): Promise<void> => {
  await api.post('/auth/logout');
  localStorage.removeItem('token');
};
```

### Tasks

```typescript
// Get all tasks with filters
const getTasks = async (filters?: TaskFilters): Promise<PaginatedResponse<Task>> => {
  const response = await api.get('/tasks', { params: filters });
  return response.data;
};

// Get single task
const getTask = async (id: number): Promise<Task> => {
  const response = await api.get(`/tasks/${id}`);
  return response.data;
};

// Create task
const createTask = async (taskData: Partial<Task>): Promise<Task> => {
  const response = await api.post('/tasks', taskData);
  return response.data;
};

// Update task
const updateTask = async (id: number, taskData: Partial<Task>): Promise<Task> => {
  const response = await api.put(`/tasks/${id}`, taskData);
  return response.data;
};

// Delete task
const deleteTask = async (id: number): Promise<void> => {
  await api.delete(`/tasks/${id}`);
};
```

### Dashboard

```typescript
// Get dashboard metrics
const getDashboard = async (): Promise<DashboardData> => {
  const response = await api.get('/dashboard');
  return response.data;
};
```

## 🎣 Custom Hooks Pattern

### useDashboard Hook

```typescript
import { useState, useEffect } from 'react';
import { apiService } from '../services/api';

export const useDashboard = () => {
  const [metrics, setMetrics] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await apiService.getDashboard();
      setMetrics(data);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to load dashboard data');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDashboardData();
  }, []);

  return {
    metrics,
    loading,
    error,
    refetch: fetchDashboardData,
  };
};
```

### useTaskFilters Hook

```typescript
import { useState, useMemo } from 'react';
import { Task, TaskFilters } from '../types';

export const useTaskFilters = (tasks: Task[]) => {
  const [filters, setFilters] = useState<TaskFilters>({
    status: '',
    priority: '',
    search: '',
    assigned_to: undefined,
  });

  const filteredTasks = useMemo(() => {
    return tasks.filter((task) => {
      if (filters.status && task.status !== filters.status) return false;
      if (filters.priority && task.priority !== filters.priority) return false;
      if (filters.search && !task.title.toLowerCase().includes(filters.search.toLowerCase())) return false;
      if (filters.assigned_to && task.assigned_to !== filters.assigned_to) return false;
      return true;
    });
  }, [tasks, filters]);

  return {
    filters,
    setFilters,
    filteredTasks,
  };
};
```

## 🔄 Data Flow Patterns

### 1. Component with API Call

```typescript
import React, { useState, useEffect } from 'react';
import { apiService } from '../services/api';
import { Task } from '../types';
import LoadingSpinner from '../components/common/LoadingSpinner';

const TaskList: React.FC = () => {
  const [tasks, setTasks] = useState<Task[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchTasks = async () => {
      try {
        setLoading(true);
        const response = await apiService.getTasks();
        setTasks(response.data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Failed to load tasks');
      } finally {
        setLoading(false);
      }
    };

    fetchTasks();
  }, []);

  if (loading) return <LoadingSpinner />;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      {tasks.map((task) => (
        <TaskItem key={task.id} task={task} />
      ))}
    </div>
  );
};
```

### 2. Form Submission

```typescript
import React, { useState } from 'react';
import { apiService } from '../services/api';
import { Task } from '../types';

const TaskForm: React.FC = () => {
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    priority: 'medium',
    due_date: '',
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      setLoading(true);
      setError(null);

      const newTask = await apiService.createTask(formData);
      // Handle success (redirect, show notification, etc.)

    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to create task');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {/* Form fields */}
      {error && <div className="error">{error}</div>}
      <button type="submit" disabled={loading}>
        {loading ? 'Creating...' : 'Create Task'}
      </button>
    </form>
  );
};
```

## 🛡️ Error Handling

### Global Error Interceptor

```typescript
// In api.ts
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const message = error.response?.data?.message || 'An error occurred';

    // Handle specific error types
    switch (error.response?.status) {
      case 401:
        // Unauthorized - redirect to login
        localStorage.removeItem('token');
        window.location.href = '/login';
        break;
      case 403:
        // Forbidden - show access denied message
        console.error('Access denied');
        break;
      case 404:
        // Not found - show not found message
        console.error('Resource not found');
        break;
      case 422:
        // Validation errors - handled by form components
        break;
      case 500:
        // Server error - show generic error
        console.error('Server error');
        break;
    }

    return Promise.reject(error);
  }
);
```

### Component-Level Error Handling

```typescript
const useApiCall = <T>(apiCall: () => Promise<T>) => {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const execute = async () => {
    try {
      setLoading(true);
      setError(null);
      const result = await apiCall();
      setData(result);
    } catch (err: any) {
      setError(err.response?.data?.message || 'An error occurred');
    } finally {
      setLoading(false);
    }
  };

  return { data, loading, error, execute };
};
```

## 🔐 Authentication Flow

### Token Management

```typescript
// Store token on login
const handleLogin = async (credentials: LoginCredentials) => {
  try {
    const response = await apiService.login(credentials);
    localStorage.setItem('token', response.token);
    localStorage.setItem('user', JSON.stringify(response.user));
    // Redirect to dashboard
  } catch (error) {
    // Handle login error
  }
};

// Check authentication status
const isAuthenticated = (): boolean => {
  const token = localStorage.getItem('token');
  return !!token;
};

// Clear authentication on logout
const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  // Redirect to login
};
```

### Protected Routes

```typescript
import { Navigate } from 'react-router-dom';

const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const isAuth = isAuthenticated();

  if (!isAuth) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
};
```

## 📊 Data Caching

### Simple Cache Implementation

```typescript
class ApiCache {
  private cache = new Map<string, { data: any; timestamp: number }>();
  private ttl = 5 * 60 * 1000; // 5 minutes

  set(key: string, data: any): void {
    this.cache.set(key, { data, timestamp: Date.now() });
  }

  get(key: string): any | null {
    const item = this.cache.get(key);
    if (!item) return null;

    if (Date.now() - item.timestamp > this.ttl) {
      this.cache.delete(key);
      return null;
    }

    return item.data;
  }

  clear(): void {
    this.cache.clear();
  }
}

const apiCache = new ApiCache();
```

### Cached API Calls

```typescript
const getTasksWithCache = async (filters?: TaskFilters): Promise<Task[]> => {
  const cacheKey = `tasks-${JSON.stringify(filters)}`;
  const cached = apiCache.get(cacheKey);

  if (cached) {
    return cached;
  }

  const response = await apiService.getTasks(filters);
  apiCache.set(cacheKey, response.data);

  return response.data;
};
```

## 🧪 Testing API Integration

### Mock API Service

```typescript
// __mocks__/api.ts
export const apiService = {
  getTasks: jest.fn(),
  createTask: jest.fn(),
  updateTask: jest.fn(),
  deleteTask: jest.fn(),
  getDashboard: jest.fn(),
};
```

### Component Testing

```typescript
import { render, screen, waitFor } from '@testing-library/react';
import { apiService } from '../services/api';
import TaskList from '../components/TaskList';

jest.mock('../services/api');

test('renders tasks from API', async () => {
  const mockTasks = [
    { id: 1, title: 'Task 1', status: 'pending' },
    { id: 2, title: 'Task 2', status: 'completed' },
  ];

  (apiService.getTasks as jest.Mock).mockResolvedValue({
    data: mockTasks,
  });

  render(<TaskList />);

  await waitFor(() => {
    expect(screen.getByText('Task 1')).toBeInTheDocument();
    expect(screen.getByText('Task 2')).toBeInTheDocument();
  });
});
```

## 🚀 Performance Optimization

### Request Debouncing

```typescript
import { useCallback } from 'react';
import { debounce } from 'lodash';

const useDebouncedSearch = (onSearch: (query: string) => void, delay = 300) => {
  const debouncedSearch = useCallback(
    debounce((query: string) => {
      onSearch(query);
    }, delay),
    [onSearch, delay]
  );

  return debouncedSearch;
};
```

### Optimistic Updates

```typescript
const updateTaskOptimistically = async (id: number, updates: Partial<Task>) => {
  // Update UI immediately
  setTasks(prev => prev.map(task =>
    task.id === id ? { ...task, ...updates } : task
  ));

  try {
    // Make API call
    await apiService.updateTask(id, updates);
  } catch (error) {
    // Revert on error
    setTasks(prev => prev.map(task =>
      task.id === id ? { ...task, ...originalData } : task
    ));
    throw error;
  }
};
```

## 📝 Best Practices

1. **Always handle loading and error states**
2. **Use TypeScript for API responses**
3. **Implement proper error boundaries**
4. **Cache frequently accessed data**
5. **Use optimistic updates for better UX**
6. **Debounce search and filter requests**
7. **Implement retry logic for failed requests**
8. **Log API errors for debugging**
9. **Use environment variables for API URLs**
10. **Test API integration thoroughly**
