import React from "react";
import LoadingSpinner from "../common/LoadingSpinner";
import { useDashboard } from "../../hooks";
import {
  DashboardHeader,
  MetricsCards,
  CompletionRate,
  RecentActivity,
  UpcomingDeadlines,
  ErrorMessage,
} from "./index";

const Dashboard: React.FC = () => {
  const { metrics, loading, error } = useDashboard();

  if (loading) {
    return <LoadingSpinner />;
  }

  if (error) {
    return <ErrorMessage message={error} />;
  }

  if (!metrics) {
    return null;
  }

  return (
    <div className="space-y-6">
      <DashboardHeader />
      <MetricsCards metrics={metrics} />
      <CompletionRate
        completionRate={metrics.task_statistics.completion_rate}
      />

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <RecentActivity activities={metrics.recent_activity} />
        <UpcomingDeadlines deadlines={metrics.upcoming_deadlines} />
      </div>
    </div>
  );
};

export default Dashboard;
