# Task Management Frontend

A modern React TypeScript application for task management with a clean, modular architecture and comprehensive dashboard.

## 🚀 Quick Start

### Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- Backend API running (see backend README)

### Installation

```bash
cd frontend
npm install
```

### Development

```bash
npm run dev
```

The application will be available at `http://localhost:5173`

### Build for Production

```bash
npm run build
```

### Preview Production Build

```bash
npm run preview
```

## 🏗️ Architecture

### Tech Stack

- **React 18** - UI framework
- **TypeScript** - Type safety
- **Vite** - Build tool and dev server
- **Tailwind CSS** - Styling
- **React Router** - Navigation
- **Axios** - HTTP client

### Project Structure

```
frontend/
├── public/                 # Static assets
├── src/
│   ├── components/         # React components
│   │   ├── about/         # About page components
│   │   ├── auth/          # Authentication components
│   │   ├── common/        # Reusable UI components
│   │   ├── contact/       # Contact page components
│   │   ├── dashboard/     # Dashboard components (refactored)
│   │   ├── layout/        # Layout components
│   │   └── tasks/         # Task management components
│   ├── contexts/          # React contexts
│   ├── hooks/             # Custom React hooks
│   ├── services/          # API services
│   ├── types/             # TypeScript type definitions
│   ├── App.tsx            # Main app component
│   └── index.tsx          # App entry point
├── package.json
├── tailwind.config.js     # Tailwind configuration
├── tsconfig.json          # TypeScript configuration
└── vite.config.ts         # Vite configuration
```

## 📦 Components

### Dashboard Components (Refactored)

The dashboard has been refactored into modular components:

- **`Dashboard.tsx`** - Main orchestrator component
- **`DashboardHeader.tsx`** - Header with title and "New Task" button
- **`MetricsCards.tsx`** - Grid of metric cards (Total, Completed, Pending, Overdue)
- **`CompletionRate.tsx`** - Progress bar showing completion rate
- **`RecentActivity.tsx`** - List of recent task activities
- **`UpcomingDeadlines.tsx`** - List of upcoming deadlines
- **`ErrorMessage.tsx`** - Reusable error component
- **`utils.ts`** - Shared utility functions

### Custom Hooks

- **`useDashboard`** - Dashboard data fetching and state management
- **`useTaskFilters`** - Task filtering logic
- **`useTaskColorUtils`** - Priority and status color utilities

### Common Components

- **`LoadingSpinner`** - Loading indicator
- **`Pagination`** - Pagination controls
- **`SearchInput`** - Search functionality
- **`FilterSelect`** - Filter dropdowns

## 🔧 Development

### Adding New Components

1. Create component in appropriate directory
2. Add TypeScript interfaces for props
3. Export from `index.ts` file
4. Add to main component imports

### Styling Guidelines

- Use Tailwind CSS classes
- Follow mobile-first responsive design
- Maintain consistent spacing and colors
- Use semantic color names (e.g., `text-gray-700`)

### TypeScript Best Practices

- Define interfaces for all props
- Use strict type checking
- Avoid `any` types
- Export types from `types/index.ts`

### API Integration

- Use `services/api.ts` for all API calls
- Handle loading and error states
- Use proper TypeScript types for responses

## 🧪 Testing

### Running Tests

```bash
npm test
```

### Test Structure

- Unit tests for utilities and hooks
- Component tests for UI components
- Integration tests for API calls

## 📱 Features

### Authentication

- Login/Register forms
- JWT token management
- Protected routes
- User context

### Task Management

- Create, edit, delete tasks
- Task filtering and search
- Priority and status management
- Due date handling

### Dashboard

- Real-time metrics
- Recent activity feed
- Upcoming deadlines
- Completion rate tracking

### Responsive Design

- Mobile-first approach
- Tablet and desktop optimized
- Touch-friendly interactions

## 🔒 Security

### Best Practices

- Input validation
- XSS prevention
- CSRF protection
- Secure API communication

### Environment Variables

Create a `.env` file:

```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME=Task Management
```

## 🚀 Deployment

### Build Process

1. Run `npm run build`
2. Deploy `dist/` folder to web server
3. Configure server for SPA routing

### Environment Configuration

- Set production API URL
- Configure CDN for static assets
- Enable HTTPS

## 🤝 Contributing

### Code Style

- Use Prettier for formatting
- Follow ESLint rules
- Write meaningful commit messages
- Add JSDoc comments for complex functions

### Git Workflow

1. Create feature branch
2. Make changes
3. Add tests
4. Submit pull request

## 📚 Additional Resources

- [React Documentation](https://react.dev/)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vite Documentation](https://vitejs.dev/)

## 🐛 Troubleshooting

### Common Issues

- **Port conflicts**: Change port in `vite.config.ts`
- **API errors**: Check backend is running and CORS is configured
- **Build errors**: Clear `node_modules` and reinstall

### Getting Help

- Check existing issues
- Review documentation
- Contact development team
