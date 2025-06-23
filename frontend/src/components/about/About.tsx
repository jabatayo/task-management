import React, { useEffect, useState } from "react";
import { apiService } from "../../services/api";
import { AboutInfo } from "../../types";
import LoadingSpinner from "../common/LoadingSpinner";

const About: React.FC = () => {
  const [about, setAbout] = useState<AboutInfo | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchAbout();
  }, []);

  const fetchAbout = async () => {
    try {
      setLoading(true);
      const data = await apiService.getAbout();
      setAbout(data);
    } catch (err: any) {
      setError(err.response?.data?.message || "Failed to load about info");
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner />;
  if (error)
    return (
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
              Error loading about info
            </h3>
            <div className="mt-2 text-sm text-red-700">{error}</div>
          </div>
        </div>
      </div>
    );
  if (!about) return null;

  return (
    <div className="max-w-3xl mx-auto mt-10 space-y-8">
      <div className="bg-white p-6 rounded-lg shadow">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          About {about.name}
        </h1>
        <p className="text-gray-600 mb-4">{about.description}</p>
        <div className="mb-4">
          <span className="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full font-semibold">
            Version {about.version}
          </span>
        </div>
        <h2 className="text-xl font-semibold text-gray-800 mt-6 mb-2">
          Features
        </h2>
        <ul className="list-disc list-inside text-gray-700 space-y-1">
          {about.features.map((feature, idx) => (
            <li key={idx}>{feature}</li>
          ))}
        </ul>
      </div>
      <div className="bg-white p-6 rounded-lg shadow">
        <h2 className="text-xl font-semibold text-gray-800 mb-2">Our Team</h2>
        <ul className="divide-y divide-gray-200">
          {about.team.map((member, idx) => (
            <li key={idx} className="py-2 flex items-center">
              <div className="flex-shrink-0 h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                <span className="text-sm font-medium text-gray-700">
                  {member.name.charAt(0).toUpperCase()}
                </span>
              </div>
              <div>
                <div className="text-sm font-medium text-gray-900">
                  {member.name}
                </div>
                <div className="text-xs text-gray-500">{member.role}</div>
                <div className="text-xs text-gray-400">{member.email}</div>
              </div>
            </li>
          ))}
        </ul>
      </div>
      <div className="bg-white p-6 rounded-lg shadow">
        <h2 className="text-xl font-semibold text-gray-800 mb-2">Contact</h2>
        <div className="text-gray-700">
          <div>
            Email:{" "}
            <a
              href={`mailto:${about.contact.email}`}
              className="text-indigo-600 hover:underline"
            >
              {about.contact.email}
            </a>
          </div>
          {about.contact.phone && <div>Phone: {about.contact.phone}</div>}
          {about.contact.address && <div>Address: {about.contact.address}</div>}
        </div>
      </div>
    </div>
  );
};

export default About;
