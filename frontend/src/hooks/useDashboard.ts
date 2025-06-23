import { useState, useEffect } from "react";
import { apiService } from "../services/api";

// Import the DashboardData interface from the API service
type DashboardData = Awaited<ReturnType<typeof apiService.getDashboard>>;

interface UseDashboardReturn {
  metrics: DashboardData | null;
  loading: boolean;
  error: string | null;
  refetch: () => Promise<void>;
}

export const useDashboard = (): UseDashboardReturn => {
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
      setError(err.response?.data?.message || "Failed to load dashboard data");
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
