# Component Guide

This guide explains how to use and extend the components in the Task Management frontend.

## 🎯 Component Philosophy

Our components follow these principles:

- **Single Responsibility**: Each component has one clear purpose
- **Reusability**: Components are designed to be reused across the app
- **Type Safety**: All components use TypeScript interfaces
- **Accessibility**: Components follow WCAG guidelines
- **Responsive**: Mobile-first design approach

## 📦 Component Categories

### 1. Layout Components (`src/components/layout/`)

#### Layout.tsx

Main layout wrapper that provides the overall page structure.

```tsx
import { Layout } from '../components/layout';

<Layout>
  <YourPageContent />
</Layout>
```

#### Header.tsx

Top navigation bar with user menu and notifications.

#### Sidebar.tsx

Left sidebar navigation with menu items.

#### MobileSidebar.tsx

Mobile-responsive sidebar that slides in from the left.

### 2. Dashboard Components (`src/components/dashboard/`)

#### Dashboard.tsx

Main dashboard orchestrator that combines all dashboard sections.

```tsx
import { Dashboard } from '../components/dashboard';

<Dashboard />
```

#### MetricsCards.tsx

Grid of metric cards showing key statistics.

**Props:**

```tsx
interface MetricsCardsProps {
  metrics: {
    task_statistics: {
      total_tasks: number;
      completed_tasks: number;
      pending_tasks: number;
      completion_rate: number;
    };
    overdue_tasks: any[];
  };
}
```

#### CompletionRate.tsx

Progress bar showing task completion percentage.

**Props:**

```tsx
interface CompletionRateProps {
  completionRate: number;
}
```

#### RecentActivity.tsx

List of recent task activities with user avatars.

**Props:**

```tsx
interface RecentActivityProps {
  activities: Array<{
    id: number;
    title: string;
    status: string;
    priority: string;
    updated_at: string;
  }>;
}
```

#### UpcomingDeadlines.tsx

List of tasks with upcoming deadlines.

**Props:**

```tsx
interface UpcomingDeadlinesProps {
  deadlines: Array<{
    id: number;
    title: string;
    priority: string;
    due_date: string;
  }>;
}
```

### 3. Task Components (`src/components/tasks/`)

#### TaskList.tsx

Main task listing component with filtering and pagination.

#### TaskItem.tsx

Individual task item with status, priority, and metadata.

**Props:**

```tsx
interface TaskItemProps {
  task: Task;
  getPriorityColor: (priority: string) => string;
  getStatusColor: (status: string) => string;
  formatDate: (dateString: string) => string;
}
```

#### TaskForm.tsx

Form for creating and editing tasks.

#### TaskDetail.tsx

Detailed view of a single task.

### 4. Common Components (`src/components/common/`)

#### LoadingSpinner.tsx

Reusable loading indicator.

```tsx
import { LoadingSpinner } from '../components/common';

<LoadingSpinner />
```

#### Pagination.tsx

Pagination controls for lists.

**Props:**

```tsx
interface PaginationProps {
  currentPage: number;
  totalPages: number;
  onPageChange: (page: number) => void;
}
```

#### SearchInput.tsx

Search input with debounced search functionality.

**Props:**

```tsx
interface SearchInputProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  debounceMs?: number;
}
```

#### FilterSelect.tsx

Dropdown filter component.

**Props:**

```tsx
interface FilterSelectProps {
  options: Array<{ value: string; label: string }>;
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
}
```

### 5. Authentication Components (`src/components/auth/`)

#### Login.tsx

User login form with validation.

#### Register.tsx

User registration form with validation.

## 🔧 Creating New Components

### 1. Component Structure

```tsx
import React from 'react';

interface YourComponentProps {
  // Define your props here
  title: string;
  onAction?: () => void;
}

const YourComponent: React.FC<YourComponentProps> = ({
  title,
  onAction
}) => {
  return (
    <div className="your-component">
      <h2>{title}</h2>
      {onAction && (
        <button onClick={onAction}>
          Action
        </button>
      )}
    </div>
  );
};

export default YourComponent;
```

### 2. Export from Index

Add to the appropriate `index.ts` file:

```tsx
export { default as YourComponent } from './YourComponent';
```

### 3. Add to Main Imports

Import in the main component:

```tsx
import { YourComponent } from './components/your-category';
```

## 🎨 Styling Guidelines

### Tailwind CSS Classes

- Use semantic color names: `text-gray-700`, `bg-blue-500`
- Follow spacing scale: `p-4`, `m-2`, `space-y-4`
- Use responsive prefixes: `sm:`, `md:`, `lg:`, `xl:`

### Component-Specific Styles

```tsx
// Good: Component-specific classes
<div className="task-item bg-white shadow rounded-lg p-4">

// Avoid: Generic classes without context
<div className="bg-white shadow rounded-lg p-4">
```

### Responsive Design

```tsx
// Mobile-first approach
<div className="w-full md:w-1/2 lg:w-1/3">
  <div className="text-sm md:text-base lg:text-lg">
    Content
  </div>
</div>
```

## 🔄 State Management

### Local State

Use `useState` for component-specific state:

```tsx
const [isOpen, setIsOpen] = useState(false);
const [data, setData] = useState<DataType[]>([]);
```

### Global State

Use React Context for app-wide state:

```tsx
// contexts/AuthContext.tsx
const AuthContext = createContext<AuthContextType | undefined>(undefined);
```

### Custom Hooks

Extract reusable logic into custom hooks:

```tsx
// hooks/useTaskFilters.ts
export const useTaskFilters = () => {
  // Filter logic here
  return { filters, setFilters, filteredTasks };
};
```

## 🧪 Testing Components

### Component Testing

```tsx
import { render, screen } from '@testing-library/react';
import YourComponent from './YourComponent';

test('renders component with title', () => {
  render(<YourComponent title="Test Title" />);
  expect(screen.getByText('Test Title')).toBeInTheDocument();
});
```

### Hook Testing

```tsx
import { renderHook } from '@testing-library/react-hooks';
import { useYourHook } from './useYourHook';

test('hook returns expected values', () => {
  const { result } = renderHook(() => useYourHook());
  expect(result.current.value).toBe(expectedValue);
});
```

## 📱 Accessibility

### ARIA Labels

```tsx
<button aria-label="Close modal" onClick={onClose}>
  <XIcon />
</button>
```

### Keyboard Navigation

```tsx
<div
  tabIndex={0}
  onKeyDown={(e) => {
    if (e.key === 'Enter') handleClick();
  }}
>
  Clickable content
</div>
```

### Screen Reader Support

```tsx
<div role="status" aria-live="polite">
  {loading ? 'Loading...' : 'Content loaded'}
</div>
```

## 🚀 Performance Optimization

### Memoization

```tsx
const MemoizedComponent = React.memo(YourComponent);
```

### Lazy Loading

```tsx
const LazyComponent = React.lazy(() => import('./LazyComponent'));
```

### Code Splitting

```tsx
// In your routing
const Dashboard = React.lazy(() => import('./components/dashboard/Dashboard'));
```

## 🔍 Debugging

### React DevTools

- Install React Developer Tools browser extension
- Use Components tab to inspect component hierarchy
- Use Profiler tab to identify performance issues

### Console Logging

```tsx
useEffect(() => {
  console.log('Component mounted with props:', props);
}, [props]);
```

### Error Boundaries

```tsx
class ErrorBoundary extends React.Component {
  // Error boundary implementation
}
```

## 📚 Best Practices

1. **Keep components small and focused**
2. **Use TypeScript for all props and state**
3. **Follow naming conventions consistently**
4. **Write self-documenting code**
5. **Add JSDoc comments for complex logic**
6. **Test components thoroughly**
7. **Optimize for performance**
8. **Ensure accessibility compliance**
