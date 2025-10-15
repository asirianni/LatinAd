#!/bin/bash

# LatinAd Challenge - DDEV Setup Script
# This script configures the project for DDEV deployment

set -e

echo "🚀 Setting up LatinAd Challenge for DDEV..."
echo "============================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if DDEV is installed
if ! command -v ddev &> /dev/null; then
    print_error "DDEV is not installed. Please install DDEV first:"
    echo "curl -fsSL https://raw.githubusercontent.com/drud/ddev/master/scripts/install_ddev.sh | bash"
    exit 1
fi

# Check if Laravel project exists, if not create it
if [ ! -f composer.json ]; then
    print_status "Laravel project not found. Creating Laravel project..."
    composer create-project laravel/laravel . --prefer-dist --no-interaction
    print_success "Laravel project created"
else
    print_warning "Laravel project already exists, skipping creation"
fi

# Configure DDEV
print_status "Configuring DDEV..."
ddev config --project-type=laravel --docroot=public --create-docroot

# Copy environment file
print_status "Setting up environment..."
cp env.docker .env

# Start DDEV
print_status "Starting DDEV services..."
ddev start

# Install dependencies
print_status "Installing PHP dependencies..."
ddev composer install

# Generate application key
print_status "Generating application key..."
ddev artisan key:generate

# Run database migrations
print_status "Running database migrations..."
ddev artisan migrate

# Clear and cache configuration
print_status "Optimizing application..."
ddev artisan config:cache
ddev artisan route:cache
ddev artisan view:cache

print_success "DDEV environment is ready!"
echo ""
echo "🌐 Application URLs:"
ddev launch
echo ""
echo "🗄️  Database Access:"
echo "   Host: db"
echo "   Port: 3306"
echo "   Database: db"
echo "   Username: db"
echo "   Password: db"
echo ""
echo "🔴 Redis Access:"
echo "   Host: redis"
echo "   Port: 6379"
echo ""
echo "📝 Useful DDEV Commands:"
echo "   Stop services: ddev stop"
echo "   View logs: ddev logs"
echo "   Access shell: ddev ssh"
echo "   Run Artisan commands: ddev artisan [command]"
echo "   Run Composer commands: ddev composer [command]"
echo ""
print_success "Happy coding with DDEV! 🎉"
