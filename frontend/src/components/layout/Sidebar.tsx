import React from "react";
import Navigation from "./Navigation";
import UserProfile from "./UserProfile";

interface SidebarProps {
  onLogout: () => void;
  onItemClick?: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ onLogout, onItemClick }) => {
  return (
    <div className="hidden md:flex md:flex-shrink-0">
      <div className="flex flex-col w-64">
        <div className="flex flex-col h-0 flex-1 border-r border-gray-200 bg-white">
          <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
            <div className="flex items-center flex-shrink-0 px-4">
              <h1 className="text-xl font-semibold text-gray-900">
                Task Management
              </h1>
            </div>
            <Navigation onItemClick={onItemClick} />
          </div>
          <UserProfile onLogout={onLogout} />
        </div>
      </div>
    </div>
  );
};

export default Sidebar;
