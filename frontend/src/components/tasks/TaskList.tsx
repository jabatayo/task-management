import React, { useState, useEffect, useRef, useCallback } from "react";
import { Link } from "react-router-dom";
import { apiService } from "../../services/api";
import { Task, PaginatedResponse } from "../../types";
import { LoadingSpinner, Pagination } from "../common";
import { TaskItem, TaskFilters } from "./";
import { useTaskFilters, useTaskColorUtils } from "../../hooks";

const TaskList: React.FC = () => {
  const [tasks, setTasks] = useState<Task[]>([]);
  const [pagination, setPagination] = useState<{
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  } | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const searchInputRef = useRef<HTMLInputElement | null>(null);
  const wasSearchFocused = useRef(false);
  const isFetching = useRef(false);
  const lastFiltersRef = useRef<string>("");

  const {
    filters,
    searchTerm,
    handleFilterChange,
    handleSearchChange,
    updateSearchFilter,
  } = useTaskFilters();

  const { getPriorityColor, getStatusColor, formatDate } = useTaskColorUtils();

  // Debounced search effect
  useEffect(() => {
    const timeoutId = setTimeout(() => {
      updateSearchFilter(searchTerm);
    }, 300);

    return () => clearTimeout(timeoutId);
  }, [searchTerm, updateSearchFilter]);

  // Fetch tasks effect
  useEffect(() => {
    const filtersString = JSON.stringify(filters);

    // Only trigger if filters actually changed
    if (lastFiltersRef.current === filtersString) {
      return;
    }

    // Update the last filters reference
    lastFiltersRef.current = filtersString;

    // Use an IIFE to avoid dependency on fetchTasks function reference
    (async () => {
      // Prevent multiple simultaneous requests
      if (isFetching.current) {
        return;
      }

      isFetching.current = true;

      try {
        // Small delay to prevent rapid loading state changes
        const loadingTimeout = setTimeout(() => {
          setLoading(true);
          setError(null);
        }, 100);

        const response = await apiService.getTasks(filters);
        clearTimeout(loadingTimeout);

        // Batch state updates to prevent multiple re-renders
        setLoading(false);
        setTasks(response.data);
        setPagination({
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
          from: response.from,
          to: response.to,
        });
      } catch (err: any) {
        setError(err.response?.data?.message || "Failed to load tasks");
        setLoading(false);
      } finally {
        isFetching.current = false;
      }
    })();
  }, [filters]);

  // Focus restoration effect
  useEffect(() => {
    if (wasSearchFocused.current && searchInputRef.current && !loading) {
      searchInputRef.current.focus();
      // Restore cursor position if possible
      const length = searchInputRef.current.value.length;
      searchInputRef.current.setSelectionRange(length, length);
    }
  }, [loading, tasks]);

  const handlePageChange = (page: number) => {
    handleFilterChange("page", page);
  };

  const handleSearchFocus = () => {
    wasSearchFocused.current = true;
  };

  const handleSearchBlur = () => {
    wasSearchFocused.current = false;
  };

  const setSearchInputRef = (ref: HTMLInputElement | null) => {
    searchInputRef.current = ref;
  };

  if (loading) {
    return <LoadingSpinner />;
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Tasks</h1>
          <p className="mt-2 text-sm text-gray-700">
            Manage and track your tasks
          </p>
        </div>
        <div className="mt-4 sm:mt-0">
          <Link
            to="/tasks/new"
            className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              className="-ml-1 mr-2 h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
            New Task
          </Link>
        </div>
      </div>

      {/* Filters */}
      <TaskFilters
        searchTerm={searchTerm}
        onSearchChange={handleSearchChange}
        filters={filters}
        onFilterChange={handleFilterChange}
        onSearchRef={setSearchInputRef}
        onSearchFocus={handleSearchFocus}
        onSearchBlur={handleSearchBlur}
      />

      {/* Error Message */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-md p-4">
          <div className="flex">
            <div className="flex-shrink-0">
              <svg
                className="h-5 w-5 text-red-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fillRule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clipRule="evenodd"
                />
              </svg>
            </div>
            <div className="ml-3">
              <h3 className="text-sm font-medium text-red-800">
                Error loading tasks
              </h3>
              <div className="mt-2 text-sm text-red-700">{error}</div>
            </div>
          </div>
        </div>
      )}

      {/* Tasks Table */}
      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        <div className="overflow-x-auto">
          <div className="min-w-[600px]">
            <ul className="divide-y divide-gray-200">
              {tasks.map((task) => (
                <TaskItem
                  key={task.id}
                  task={task}
                  getPriorityColor={getPriorityColor}
                  getStatusColor={getStatusColor}
                  formatDate={formatDate}
                />
              ))}
            </ul>
          </div>
        </div>
      </div>

      {/* Empty State */}
      {tasks.length === 0 && !loading && !error && (
        <div className="text-center py-12">
          <svg
            className="mx-auto h-12 w-12 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            />
          </svg>
          <h3 className="mt-2 text-sm font-medium text-gray-900">
            No tasks found
          </h3>
          <p className="mt-1 text-sm text-gray-500">
            Get started by creating a new task.
          </p>
          <div className="mt-6">
            <Link
              to="/tasks/new"
              className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                className="-ml-1 mr-2 h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                />
              </svg>
              New Task
            </Link>
          </div>
        </div>
      )}

      {/* Pagination */}
      {pagination && (
        <Pagination pagination={pagination} onPageChange={handlePageChange} />
      )}
    </div>
  );
};

export default TaskList;
