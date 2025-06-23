export interface User {
  id: number;
  name: string;
  email: string;
  roles: Role[];
  created_at: string;
  updated_at: string;
}

export interface Role {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

export enum TaskStatus {
  PENDING = "pending",
  IN_PROGRESS = "in_progress",
  COMPLETED = "completed",
  CANCELLED = "cancelled",
}

export enum TaskPriority {
  LOW = "low",
  MEDIUM = "medium",
  HIGH = "high",
  URGENT = "urgent",
}

export interface Task {
  id: number;
  title: string;
  description?: string;
  status: TaskStatus;
  priority: TaskPriority;
  due_date?: string;
  created_by: number;
  assigned_to?: number;
  created_by_user?: User;
  assigned_to_user?: User;
  creator?: { id: number; name: string };
  assignee?: { id: number; name: string } | null;
  created_at: string;
  updated_at: string;
}

export interface DashboardMetrics {
  total_tasks: number;
  completed_tasks: number;
  pending_tasks: number;
  overdue_tasks: number;
  completion_rate: number;
  average_completion_time: number;
  recent_activity: Task[];
  priority_distribution: Record<string, number>;
  status_distribution: Record<string, number>;
  overdue_tasks_list: Task[];
  upcoming_deadlines: Task[];
}

export interface ContactMessage {
  id: number;
  name: string;
  email: string;
  message: string;
  user_id?: number;
  created_at: string;
  updated_at: string;
}

export interface AboutInfo {
  name: string;
  version: string;
  description: string;
  features: string[];
  team: TeamMember[];
  contact: ContactInfo;
}

export interface TeamMember {
  name: string;
  role: string;
  email: string;
}

export interface ContactInfo {
  email: string;
  phone?: string;
  address?: string;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
  status: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

export interface TaskFilters {
  status?: string;
  priority?: string;
  search?: string;
  assigned_to?: number;
  due_date_from?: string;
  due_date_to?: string;
  page?: number;
  per_page?: number;
  sort_by?: string;
  sort_order?: "asc" | "desc";
}
