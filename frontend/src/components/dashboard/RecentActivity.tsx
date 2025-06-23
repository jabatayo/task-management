import React from "react";
import { Link } from "react-router-dom";
import { getPriorityColor, formatDate } from "./utils";

interface Task {
  id: number;
  title: string;
  status: string;
  priority: string;
  updated_at: string;
  assignee?: {
    id: number;
    name: string;
  };
}

interface RecentActivityProps {
  activities: Task[];
}

const RecentActivity: React.FC<RecentActivityProps> = ({ activities }) => {
  return (
    <div className="bg-white shadow rounded-lg">
      <div className="px-4 py-5 sm:p-6">
        <h3 className="text-lg leading-6 font-medium text-gray-900">
          Recent Activity
        </h3>
        <div className="mt-6 flow-root">
          <ul className="-my-5 divide-y divide-gray-200">
            {activities.slice(0, 5).map((task) => (
              <li key={task.id} className="py-4">
                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0">
                    <div className="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                      <span className="text-sm font-medium text-gray-700">
                        {task.assignee?.name?.charAt(0).toUpperCase()}
                      </span>
                    </div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 truncate">
                      <Link
                        to={`/tasks/${task.id}`}
                        className="hover:text-indigo-600"
                      >
                        {task.title}
                      </Link>
                    </p>
                    <p className="text-sm text-gray-500">
                      {task.status.replace("_", " ")} •{" "}
                      {formatDate(task.updated_at)}
                    </p>
                  </div>
                  <div>
                    <span
                      className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getPriorityColor(
                        task.priority
                      )}`}
                    >
                      {task.priority}
                    </span>
                  </div>
                </div>
              </li>
            ))}
          </ul>
        </div>
        <div className="mt-6">
          <Link
            to="/tasks"
            className="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            View all tasks
          </Link>
        </div>
      </div>
    </div>
  );
};

export default RecentActivity;
