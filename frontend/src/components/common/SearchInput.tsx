import React, { memo } from "react";

interface SearchInputProps {
  value: string;
  onChange: (value: string) => void;
  onRef?: (ref: HTMLInputElement | null) => void;
  onFocus?: () => void;
  onBlur?: () => void;
  placeholder?: string;
  label?: string;
  id?: string;
}

const SearchInput = memo<SearchInputProps>(
  ({
    value,
    onChange,
    onRef,
    onFocus,
    onBlur,
    placeholder = "Search...",
    label = "Search",
    id = "search",
  }) => (
    <div>
      <label htmlFor={id} className="block text-sm font-medium text-gray-700">
        {label}
      </label>
      <input
        ref={onRef}
        type="text"
        id={id}
        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-3"
        placeholder={placeholder}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        onFocus={onFocus}
        onBlur={onBlur}
      />
    </div>
  )
);

SearchInput.displayName = "SearchInput";

export default SearchInput;
