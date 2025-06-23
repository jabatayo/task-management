#!/bin/bash

# Task Management API Setup Script
# This script automates the setup process for the Laravel backend

set -e

echo "🚀 Task Management API Setup"
echo "============================"

# Check if we're in the right directory
if [ ! -f "backend/artisan" ]; then
    echo "❌ Error: Please run this script from the project root directory"
    exit 1
fi

# Check prerequisites
echo "📋 Checking prerequisites..."

if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed"
    exit 1
fi

if ! command -v mysql &> /dev/null; then
    echo "❌ Error: MySQL is not installed"
    exit 1
fi

echo "✅ Prerequisites check passed"

# Navigate to backend directory
cd backend

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "⚠️  Please configure your .env file with database credentials"
    echo "   Key settings to update:"
    echo "   - DB_DATABASE"
    echo "   - DB_USERNAME"
    echo "   - DB_PASSWORD"
    echo ""
    read -p "Press Enter after configuring .env file..."
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database with default data..."
php artisan db:seed --force

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "📋 Default users created:"
echo "   Admin: admin@taskmanagement.com / password123"
echo "   User:  user@taskmanagement.com / password123"
echo ""
echo "🚀 Start the development server:"
echo "   cd backend && php artisan serve"
echo ""
echo "📚 API Documentation:"
echo "   - Main README: README.md"
echo "   - API Docs: docs/api.md"
echo ""
echo "🔐 Security Features:"
echo "   - Rate limiting enabled"
echo "   - Security headers configured"
echo "   - Role-based access control"
echo "   - Input validation"
echo ""
echo "🎉 Happy coding!" 