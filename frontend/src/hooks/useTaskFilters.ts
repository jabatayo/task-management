import { useState, useCallback, useMemo } from "react";
import { TaskFilters, TaskStatus, TaskPriority } from "../types";

export const useTaskFilters = () => {
  const [filterValues, setFilterValues] = useState({
    page: 1,
    per_page: 10,
    search: "",
    status: undefined as string | undefined,
    priority: undefined as string | undefined,
    sort_by: undefined as string | undefined,
    sort_order: "desc" as "asc" | "desc",
    assigned_to: undefined as number | undefined,
    due_date_from: undefined as string | undefined,
    due_date_to: undefined as string | undefined,
  });

  const [searchTerm, setSearchTerm] = useState("");

  // Memoize the filters object to prevent unnecessary re-renders
  const filters = useMemo(() => {
    return {
      page: filterValues.page,
      per_page: filterValues.per_page,
      search: filterValues.search,
      status: filterValues.status,
      priority: filterValues.priority,
      sort_by: filterValues.sort_by,
      sort_order: filterValues.sort_order,
      assigned_to: filterValues.assigned_to,
      due_date_from: filterValues.due_date_from,
      due_date_to: filterValues.due_date_to,
    };
  }, [filterValues]);

  const handleFilterChange = useCallback(
    (key: keyof TaskFilters, value: string | number) => {
      setFilterValues((prev) => {
        const newValue = value === "" ? undefined : value;

        // Only update if the value actually changed
        if (prev[key] === newValue) {
          return prev;
        }

        const newValues = {
          ...prev,
          [key]: newValue,
        };

        // Only reset page to 1 if it's not a page change
        if (key !== "page") {
          newValues.page = 1;
        }

        return newValues;
      });
    },
    []
  );

  const handleSearchChange = useCallback((value: string) => {
    setSearchTerm(value);
  }, []);

  const updateSearchFilter = useCallback((value: string) => {
    setFilterValues((prev) => ({
      ...prev,
      search: value,
      page: 1,
    }));
  }, []);

  const resetFilters = useCallback(() => {
    setFilterValues({
      page: 1,
      per_page: 10,
      search: "",
      status: undefined,
      priority: undefined,
      sort_by: undefined,
      sort_order: "desc",
      assigned_to: undefined,
      due_date_from: undefined,
      due_date_to: undefined,
    });
    setSearchTerm("");
  }, []);

  const hasActiveFilters = useMemo(() => {
    return !!(
      filters.search ||
      filters.status ||
      filters.priority ||
      filters.sort_by
    );
  }, [filters.search, filters.status, filters.priority, filters.sort_by]);

  return {
    filters,
    searchTerm,
    handleFilterChange,
    handleSearchChange,
    updateSearchFilter,
    resetFilters,
    hasActiveFilters,
  };
};
