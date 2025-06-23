import React, {
  createContext,
  useContext,
  useState,
  useEffect,
  ReactNode,
  useRef,
} from "react";
import { User, LoginCredentials, RegisterData } from "../types";
import apiService from "../services/api";

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (credentials: LoginCredentials) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => Promise<void>;
  isAuthenticated: boolean;
  isAdmin: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const initializedRef = useRef(false);

  useEffect(() => {
    // Prevent multiple initializations
    if (initializedRef.current) {
      return;
    }

    initializedRef.current = true;

    const initializeAuth = async () => {
      try {
        // Check if we already have user data in storage
        const storedUser = apiService.getUserFromStorage();
        if (storedUser) {
          setUser(storedUser);
          setLoading(false);
          return;
        }

        if (apiService.isAuthenticated()) {
          const userData = await apiService.getUser();
          setUser(userData);
          apiService.setUser(userData);
        }
      } catch (error) {
        console.error("Failed to initialize auth:", error);
        apiService.removeToken();
        apiService.removeUser();
      } finally {
        setLoading(false);
      }
    };

    initializeAuth();
  }, []);

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await apiService.login(credentials);
      apiService.setToken(response.token);
      apiService.setUser(response.user);
      setUser(response.user);
    } catch (error) {
      throw error;
    }
  };

  const register = async (data: RegisterData) => {
    try {
      const response = await apiService.register(data);
      apiService.setToken(response.token);
      apiService.setUser(response.user);
      setUser(response.user);
    } catch (error) {
      throw error;
    }
  };

  const logout = async () => {
    try {
      await apiService.logout();
    } catch (error) {
      console.error("Logout error:", error);
      // Continue with local cleanup even if backend logout fails
    } finally {
      apiService.removeToken();
      apiService.removeUser();
      setUser(null);
    }
  };

  const value: AuthContextType = {
    user,
    loading,
    login,
    register,
    logout,
    isAuthenticated: !!user,
    isAdmin:
      user?.roles?.some((role) => role.name === "Administrator") || false,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};
