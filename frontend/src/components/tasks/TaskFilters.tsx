import React, { memo } from "react";
import {
  TaskFilters as TaskFiltersType,
  TaskStatus,
  TaskPriority,
} from "../../types";
import SearchInput from "../common/SearchInput";
import FilterSelect from "../common/FilterSelect";

interface TaskFiltersProps {
  searchTerm: string;
  onSearchChange: (value: string) => void;
  filters: TaskFiltersType;
  onFilterChange: (key: keyof TaskFiltersType, value: string | number) => void;
  onSearchRef?: (ref: HTMLInputElement | null) => void;
  onSearchFocus?: () => void;
  onSearchBlur?: () => void;
}

const TaskFilters = memo<TaskFiltersProps>(
  ({
    searchTerm,
    onSearchChange,
    filters,
    onFilterChange,
    onSearchRef,
    onSearchFocus,
    onSearchBlur,
  }) => (
    <div className="bg-white shadow rounded-lg">
      <div className="px-4 py-5 sm:p-6">
        <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
          Filters
        </h3>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <SearchInput
            value={searchTerm}
            onChange={onSearchChange}
            onRef={onSearchRef}
            onFocus={onSearchFocus}
            onBlur={onSearchBlur}
            placeholder="Search tasks..."
            label="Search"
            id="search"
          />

          <FilterSelect
            label="Status"
            id="status"
            value={filters.status || ""}
            onChange={(value) => onFilterChange("status", value)}
            options={[
              { value: "", label: "All Statuses" },
              { value: TaskStatus.PENDING, label: "Pending" },
              { value: TaskStatus.IN_PROGRESS, label: "In Progress" },
              { value: TaskStatus.COMPLETED, label: "Completed" },
              { value: TaskStatus.CANCELLED, label: "Cancelled" },
            ]}
          />

          <FilterSelect
            label="Priority"
            id="priority"
            value={filters.priority || ""}
            onChange={(value) => onFilterChange("priority", value)}
            options={[
              { value: "", label: "All Priorities" },
              { value: TaskPriority.LOW, label: "Low" },
              { value: TaskPriority.MEDIUM, label: "Medium" },
              { value: TaskPriority.HIGH, label: "High" },
              { value: TaskPriority.URGENT, label: "Urgent" },
            ]}
          />

          <FilterSelect
            label="Sort By"
            id="sort_by"
            value={filters.sort_by || ""}
            onChange={(value) => onFilterChange("sort_by", value)}
            options={[
              { value: "", label: "Default" },
              { value: "created_at", label: "Created Date" },
              { value: "due_date", label: "Due Date" },
              { value: "priority", label: "Priority" },
              { value: "title", label: "Title" },
            ]}
          />

          <FilterSelect
            label="Sort Order"
            id="sort_order"
            value={filters.sort_order || "desc"}
            onChange={(value) => onFilterChange("sort_order", value)}
            options={[
              { value: "desc", label: "Descending" },
              { value: "asc", label: "Ascending" },
            ]}
          />
        </div>
      </div>
    </div>
  )
);

TaskFilters.displayName = "TaskFilters";

export default TaskFilters;
