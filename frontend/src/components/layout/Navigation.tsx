import React from "react";
import { Link, useLocation } from "react-router-dom";

interface NavigationItem {
  name: string;
  href: string;
  icon: string;
}

interface NavigationProps {
  onItemClick?: () => void;
  isMobile?: boolean;
}

const Navigation: React.FC<NavigationProps> = ({
  onItemClick,
  isMobile = false,
}) => {
  const location = useLocation();

  const navigation: NavigationItem[] = [
    {
      name: "Dashboard",
      href: "/dashboard",
      icon: "M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z",
    },
    {
      name: "Tasks",
      href: "/tasks",
      icon: "M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
    },
    {
      name: "Contact",
      href: "/contact",
      icon: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z",
    },
  ];

  const isActive = (href: string) => {
    return (
      location.pathname === href || location.pathname.startsWith(href + "/")
    );
  };

  const baseClasses = isMobile
    ? "group flex items-center px-2 py-2 text-base font-medium rounded-md"
    : "group flex items-center px-2 py-2 text-sm font-medium rounded-md";

  const iconClasses = isMobile
    ? "mr-4 flex-shrink-0 h-6 w-6"
    : "mr-3 flex-shrink-0 h-6 w-6";

  return (
    <nav
      className={`${
        isMobile ? "mt-5 px-2 space-y-1" : "mt-5 flex-1 px-2 bg-white space-y-1"
      }`}
    >
      {navigation.map((item) => (
        <Link
          key={item.name}
          to={item.href}
          className={`${baseClasses} ${
            isActive(item.href)
              ? "bg-gray-100 text-gray-900"
              : "text-gray-600 hover:bg-gray-50 hover:text-gray-900"
          }`}
          onClick={onItemClick}
        >
          <svg
            className={`${iconClasses} ${
              isActive(item.href)
                ? "text-gray-500"
                : "text-gray-400 group-hover:text-gray-500"
            }`}
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d={item.icon}
            />
          </svg>
          {item.name}
        </Link>
      ))}
    </nav>
  );
};

export default Navigation;
