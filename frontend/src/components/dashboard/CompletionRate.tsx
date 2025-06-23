import React from "react";

interface CompletionRateProps {
  completionRate: number;
}

const CompletionRate: React.FC<CompletionRateProps> = ({ completionRate }) => {
  return (
    <div className="bg-white shadow rounded-lg">
      <div className="px-4 py-5 sm:p-6">
        <h3 className="text-lg leading-6 font-medium text-gray-900">
          Completion Rate
        </h3>
        <div className="mt-2 max-w-xl text-sm text-gray-500">
          <p>Your current task completion rate</p>
        </div>
        <div className="mt-5">
          <div className="flex items-center">
            <div className="flex-1">
              <div className="bg-gray-200 rounded-full h-2">
                <div
                  className="bg-indigo-600 h-2 rounded-full"
                  style={{
                    width: `${completionRate || 0}%`,
                  }}
                ></div>
              </div>
            </div>
            <div className="ml-4 text-sm font-medium text-gray-900">
              {(completionRate || 0).toFixed(1)}%
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CompletionRate;
