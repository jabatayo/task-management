import { useCallback } from "react";
import { TaskStatus, TaskPriority } from "../types";

export const useTaskColorUtils = () => {
  const getPriorityColor = useCallback((priority: string) => {
    switch (priority) {
      case TaskPriority.URGENT:
        return "bg-red-100 text-red-800";
      case TaskPriority.HIGH:
        return "bg-orange-100 text-orange-800";
      case TaskPriority.MEDIUM:
        return "bg-yellow-100 text-yellow-800";
      case TaskPriority.LOW:
        return "bg-green-100 text-green-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  }, []);

  const getStatusColor = useCallback((status: string) => {
    switch (status) {
      case TaskStatus.COMPLETED:
        return "bg-green-100 text-green-800";
      case TaskStatus.IN_PROGRESS:
        return "bg-blue-100 text-blue-800";
      case TaskStatus.PENDING:
        return "bg-yellow-100 text-yellow-800";
      case TaskStatus.CANCELLED:
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  }, []);

  const formatDate = useCallback((dateString: string) => {
    return new Date(dateString).toLocaleDateString();
  }, []);

  return {
    getPriorityColor,
    getStatusColor,
    formatDate,
  };
};
