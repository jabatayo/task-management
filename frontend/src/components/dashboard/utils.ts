import { TaskPriority } from "../../types";

export const getPriorityColor = (priority: string) => {
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
};

export const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString();
};
