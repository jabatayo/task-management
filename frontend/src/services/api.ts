import axios, { AxiosInstance, AxiosResponse } from "axios";
import {
  User,
  Task,
  DashboardMetrics,
  ContactMessage,
  AboutInfo,
  LoginCredentials,
  RegisterData,
  AuthResponse,
  TaskFilters,
  PaginatedResponse,
} from "../types";

interface DashboardData {
  task_statistics: {
    total_tasks: number;
    completed_tasks: number;
    pending_tasks: number;
    in_progress_tasks: number;
    cancelled_tasks: number;
    completion_rate: number;
  };
  recent_activity: Array<{
    id: number;
    title: string;
    status: string;
    priority: string;
    updated_at: string;
    creator: { id: number; name: string };
    assignee?: { id: number; name: string };
  }>;
  performance_metrics: {
    tasks_created_this_month: number;
    tasks_completed_this_month: number;
    completion_rate_this_month: number;
    average_completion_time_days: number;
  };
  priority_distribution: Record<string, number>;
  status_distribution: Record<string, number>;
  overdue_tasks: Array<{
    id: number;
    title: string;
    priority: string;
    due_date: string;
    days_overdue: number;
    assignee: { id: number; name: string } | null;
  }>;
  upcoming_deadlines: Array<{
    id: number;
    title: string;
    priority: string;
    due_date: string;
    days_until_due: number;
    assignee: { id: number; name: string } | null;
  }>;
}

class ApiService {
  private api: AxiosInstance;
  private baseURL: string;

  constructor() {
    this.baseURL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";
    this.api = axios.create({
      baseURL: this.baseURL,
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    // Add request interceptor to include auth token
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem("token");
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Add response interceptor to handle auth errors
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          window.location.href = "/login";
        }
        return Promise.reject(error);
      }
    );
  }

  // Auth endpoints
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response: AxiosResponse<AuthResponse> = await this.api.post(
      "/login",
      credentials
    );
    return response.data;
  }

  async register(data: RegisterData): Promise<AuthResponse> {
    const response: AxiosResponse<AuthResponse> = await this.api.post(
      "/register",
      data
    );
    return response.data;
  }

  async logout(): Promise<void> {
    try {
      await this.api.post("/logout");
    } catch (error) {
      throw error;
    }
  }

  async getUser(): Promise<User> {
    const response: AxiosResponse<User> = await this.api.get("/user");
    return response.data;
  }

  // Task endpoints
  async getTasks(filters?: TaskFilters): Promise<PaginatedResponse<Task>> {
    const params = new URLSearchParams();
    if (filters) {
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          params.append(key, value.toString());
        }
      });
    }
    const response: AxiosResponse<any> = await this.api.get("/tasks", {
      params,
    });

    // Transform Laravel pagination response to match our interface
    const { data, meta } = response.data;
    return {
      data,
      current_page: meta.current_page,
      last_page: meta.last_page,
      per_page: meta.per_page,
      total: meta.total,
      from: meta.from,
      to: meta.to,
    };
  }

  async getTask(id: number): Promise<Task> {
    const response: AxiosResponse<{ task: Task }> = await this.api.get(
      `/tasks/${id}`
    );
    return response.data.task;
  }

  async createTask(task: Partial<Task>): Promise<Task> {
    const response: AxiosResponse<{ task: Task }> = await this.api.post(
      "/tasks",
      task
    );
    return response.data.task;
  }

  async updateTask(id: number, task: Partial<Task>): Promise<Task> {
    const response: AxiosResponse<{ task: Task }> = await this.api.put(
      `/tasks/${id}`,
      task
    );
    return response.data.task;
  }

  async deleteTask(id: number): Promise<void> {
    await this.api.delete(`/tasks/${id}`);
  }

  // Dashboard endpoints
  async getDashboard(): Promise<DashboardData> {
    const response: AxiosResponse<DashboardData> = await this.api.get(
      "/dashboard"
    );
    return response.data;
  }

  // Contact endpoints
  async submitContact(
    message: Omit<
      ContactMessage,
      "id" | "user_id" | "created_at" | "updated_at"
    >
  ): Promise<ContactMessage> {
    const response: AxiosResponse<ContactMessage> = await this.api.post(
      "/contact",
      message
    );
    return response.data;
  }

  // About endpoints
  async getAbout(): Promise<AboutInfo> {
    const response: AxiosResponse<AboutInfo> = await this.api.get("/about");
    return response.data;
  }

  // Utility methods
  setToken(token: string): void {
    localStorage.setItem("token", token);
  }

  getToken(): string | null {
    return localStorage.getItem("token");
  }

  removeToken(): void {
    localStorage.removeItem("token");
  }

  setUser(user: User): void {
    localStorage.setItem("user", JSON.stringify(user));
  }

  getUserFromStorage(): User | null {
    const userStr = localStorage.getItem("user");
    return userStr ? JSON.parse(userStr) : null;
  }

  removeUser(): void {
    localStorage.removeItem("user");
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  hasRole(roleName: string): boolean {
    const user = this.getUserFromStorage();
    return user?.roles?.some((role) => role.name === roleName) || false;
  }

  isAdmin(): boolean {
    return this.hasRole("Administrator");
  }
}

export const apiService = new ApiService();
export default apiService;
