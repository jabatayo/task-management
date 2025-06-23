# Development Setup Guide

This guide will help you set up the Task Management frontend for development.

## 🛠️ Prerequisites

### Required Software

- **Node.js** (v16 or higher)
- **npm** (v8 or higher) or **yarn** (v1.22 or higher)
- **Git** (for version control)
- **VS Code** (recommended editor)

### Node.js Installation

```bash
# Using nvm (recommended)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
nvm install 18
nvm use 18

# Or download from nodejs.org
# https://nodejs.org/en/download/
```

## 🚀 Initial Setup

### 1. Clone the Repository

```bash
git clone https://github.com/jabatayo/task-management.git
cd task-management/frontend
```

### 2. Install Dependencies

```bash
npm install
# or
yarn install
```

### 3. Environment Configuration

Create a `.env` file in the frontend directory:

```env
# API Configuration
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME=Task Management

# Development Settings
VITE_DEBUG_MODE=true
VITE_LOG_LEVEL=debug

# Feature Flags
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_DEBUG_TOOLS=true
```

### 4. Start Development Server

```bash
npm run dev
# or
yarn dev
```

The application will be available at `http://localhost:5173`

## 🔧 Development Tools

### VS Code Extensions (Recommended)

```json
{
  "recommendations": [
    "esbenp.prettier-vscode",
    "dbaeumer.vscode-eslint",
    "bradlc.vscode-tailwindcss",
    "ms-vscode.vscode-typescript-next",
    "formulahendry.auto-rename-tag",
    "christian-kohler.path-intellisense",
    "ms-vscode.vscode-json"
  ]
}
```

### VS Code Settings

Create `.vscode/settings.json`:

```json
{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "editor.codeActionsOnSave": {
    "source.fixAll.eslint": true
  },
  "typescript.preferences.importModuleSpecifier": "relative",
  "tailwindCSS.includeLanguages": {
    "typescript": "javascript",
    "typescriptreact": "javascript"
  }
}
```

## 📁 Project Structure

```
frontend/
├── public/                 # Static assets
├── src/
│   ├── components/         # React components
│   │   ├── about/         # About page
│   │   ├── auth/          # Authentication
│   │   ├── common/        # Reusable components
│   │   ├── contact/       # Contact page
│   │   ├── dashboard/     # Dashboard (refactored)
│   │   ├── layout/        # Layout components
│   │   └── tasks/         # Task management
│   ├── contexts/          # React contexts
│   ├── hooks/             # Custom hooks
│   ├── services/          # API services
│   ├── types/             # TypeScript types
│   ├── App.tsx            # Main app
│   └── index.tsx          # Entry point
├── docs/                  # Documentation
├── .env                   # Environment variables
├── .eslintrc.js          # ESLint configuration
├── .prettierrc           # Prettier configuration
├── tailwind.config.js    # Tailwind CSS config
├── tsconfig.json         # TypeScript config
├── vite.config.ts        # Vite configuration
└── package.json          # Dependencies and scripts
```

## 🎯 Development Workflow

### 1. Feature Development

```bash
# Create feature branch
git checkout -b feature/your-feature-name

# Make changes
# ... edit files ...

# Test changes
npm run dev
npm test

# Commit changes
git add .
git commit -m "feat: add your feature description"

# Push to remote
git push origin feature/your-feature-name
```

### 2. Component Development

```bash
# Create new component
mkdir src/components/your-component
touch src/components/your-component/YourComponent.tsx
touch src/components/your-component/index.ts

# Add to exports
echo "export { default as YourComponent } from './YourComponent';" >> src/components/your-component/index.ts
```

### 3. API Integration

```bash
# Add new API method to services/api.ts
# Create custom hook in hooks/
# Update types in types/index.ts
```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
npm test

# Run tests in watch mode
npm run test:watch

# Run tests with coverage
npm run test:coverage

# Run specific test file
npm test -- --testPathPattern=YourComponent.test.tsx
```

### Writing Tests

```typescript
// src/components/YourComponent.test.tsx
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import YourComponent from './YourComponent';

describe('YourComponent', () => {
  test('renders correctly', () => {
    render(<YourComponent title="Test" />);
    expect(screen.getByText('Test')).toBeInTheDocument();
  });

  test('handles user interaction', async () => {
    const user = userEvent.setup();
    const mockOnClick = jest.fn();

    render(<YourComponent onClick={mockOnClick} />);

    await user.click(screen.getByRole('button'));
    expect(mockOnClick).toHaveBeenCalled();
  });
});
```

## 🔍 Debugging

### Browser DevTools

1. **React DevTools**: Install browser extension for component inspection
2. **Network Tab**: Monitor API calls and responses
3. **Console**: Check for errors and debug logs
4. **Sources**: Set breakpoints in TypeScript code

### VS Code Debugging

Create `.vscode/launch.json`:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Launch Chrome",
      "type": "chrome",
      "request": "launch",
      "url": "http://localhost:5173",
      "webRoot": "${workspaceFolder}/src",
      "sourceMapPathOverrides": {
        "webpack:///src/*": "${webRoot}/*"
      }
    }
  ]
}
```

### Console Logging

```typescript
// Development logging
if (import.meta.env.DEV) {
  console.log('Debug info:', data);
}

// Error logging
console.error('Error occurred:', error);
```

## 🎨 Styling Development

### Tailwind CSS

```bash
# Install Tailwind CSS IntelliSense extension
# Configure in VS Code settings

# Use Tailwind classes
<div className="bg-white shadow-lg rounded-lg p-6">
  <h2 className="text-2xl font-bold text-gray-900">Title</h2>
</div>
```

### Custom CSS

```css
/* src/App.css */
.custom-component {
  @apply bg-blue-500 text-white p-4 rounded;
}
```

## 📦 Build and Deployment

### Development Build

```bash
npm run build
npm run preview
```

### Production Build

```bash
# Set production environment
export NODE_ENV=production

# Build for production
npm run build

# The build output will be in dist/
```

### Environment-Specific Builds

```bash
# Development
npm run build:dev

# Staging
npm run build:staging

# Production
npm run build:prod
```

## 🔧 Configuration Files

### Vite Configuration (`vite.config.ts`)

```typescript
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'dist',
    sourcemap: true,
  },
});
```

### TypeScript Configuration (`tsconfig.json`)

```json
{
  "compilerOptions": {
    "target": "ES2020",
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "skipLibCheck": true,
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "react-jsx",
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true
  },
  "include": ["src"],
  "references": [{ "path": "./tsconfig.node.json" }]
}
```

### ESLint Configuration (`.eslintrc.js`)

```javascript
module.exports = {
  root: true,
  env: { browser: true, es2020: true },
  extends: [
    'eslint:recommended',
    '@typescript-eslint/recommended',
    'plugin:react-hooks/recommended',
  ],
  ignorePatterns: ['dist', '.eslintrc.js'],
  parserOptions: { ecmaVersion: 'latest', sourceType: 'module' },
  plugins: ['react-refresh'],
  rules: {
    'react-refresh/only-export-components': [
      'warn',
      { allowConstantExport: true },
    ],
  },
};
```

## 🚨 Common Issues and Solutions

### Port Already in Use

```bash
# Kill process on port 5173
lsof -ti:5173 | xargs kill -9

# Or use different port
npm run dev -- --port 3000
```

### TypeScript Errors

```bash
# Check TypeScript errors
npx tsc --noEmit

# Fix auto-fixable issues
npx eslint --fix src/
```

### Build Errors

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
```

### API Connection Issues

1. Check if backend is running on `http://localhost:8000`
2. Verify CORS configuration in backend
3. Check network tab for specific error messages
4. Verify API base URL in `.env` file

## 📚 Additional Resources

### Documentation

- [React Documentation](https://react.dev/)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### Tools

- [React DevTools](https://chrome.google.com/webstore/detail/react-developer-tools/fmkadmapgofadopljbjfkapdkoienihi)
- [Redux DevTools](https://chrome.google.com/webstore/detail/redux-devtools/lmhkpmbekcpmknklioeibfkpmmfibljd)

### Community

- [React Community](https://reactjs.org/community/support.html)
- [TypeScript Community](https://www.typescriptlang.org/community/)
- [Vite Community](https://vitejs.dev/community/)

## 🤝 Contributing

### Code Style

- Follow the existing code style
- Use Prettier for formatting
- Follow ESLint rules
- Write meaningful commit messages

### Pull Request Process

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request
6. Wait for review and approval

### Commit Message Format

```
type(scope): description

feat(dashboard): add new metrics card
fix(auth): resolve login validation issue
docs(readme): update installation instructions
```
