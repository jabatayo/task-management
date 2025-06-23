import React, { memo } from "react";

interface FilterOption {
  value: string;
  label: string;
}

interface FilterSelectProps {
  label: string;
  id: string;
  value: string;
  onChange: (value: string) => void;
  options: FilterOption[];
}

const FilterSelect = memo<FilterSelectProps>(
  ({ label, id, value, onChange, options }) => (
    <div>
      <label htmlFor={id} className="block text-sm font-medium text-gray-700">
        {label}
      </label>
      <select
        id={id}
        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-3"
        value={value}
        onChange={(e) => onChange(e.target.value)}
      >
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
    </div>
  )
);

FilterSelect.displayName = "FilterSelect";

export default FilterSelect;
