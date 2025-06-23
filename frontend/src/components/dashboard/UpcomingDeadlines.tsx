import React from "react";
import { Link } from "react-router-dom";
import { getPriorityColor, formatDate } from "./utils";

interface Task {
  id: number;
  title: string;
  priority: string;
  due_date: string;
}

interface UpcomingDeadlinesProps {
  deadlines: Task[];
}

const UpcomingDeadlines: React.FC<UpcomingDeadlinesProps> = ({ deadlines }) => {
  return (
    <div className="bg-white shadow rounded-lg">
      <div className="px-4 py-5 sm:p-6">
        <h3 className="text-lg leading-6 font-medium text-gray-900">
          Upcoming Deadlines
        </h3>
        <div className="mt-6 flow-root">
          <ul className="-my-5 divide-y divide-gray-200">
            {deadlines.slice(0, 5).map((task) => (
              <li key={task.id} className="py-4">
                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0">
                    <div className="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                      <svg
                        className="h-4 w-4 text-red-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2}
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
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
                      Due: {formatDate(task.due_date)}
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
        {deadlines.length === 0 && (
          <div className="text-center py-4">
            <p className="text-sm text-gray-500">No upcoming deadlines</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default UpcomingDeadlines;
