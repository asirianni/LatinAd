#!/bin/bash

# LatinAd Challenge - Development Environment Startup Script
# This script sets up and starts the complete development environment

set -e

echo "🚀 Starting LatinAd Challenge Development Environment..."
echo "=================================================="

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

# Check if Docker is running
if ! sudo docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker first."
    exit 1
fi

# Check if Docker Compose is available
if ! command -v docker-compose &> /dev/null && ! sudo docker compose version > /dev/null 2>&1; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Determine Docker Compose command - always test permissions
if command -v docker-compose &> /dev/null; then
    # Test if docker-compose works without sudo
    if docker-compose ps > /dev/null 2>&1; then
        DOCKER_COMPOSE="docker-compose"
        print_status "Using: docker-compose"
    else
        # docker-compose exists but needs sudo
        DOCKER_COMPOSE="sudo docker-compose"
        print_status "Using: sudo docker-compose"
    fi
elif sudo docker compose version > /dev/null 2>&1; then
    # Use new docker compose command with sudo
    DOCKER_COMPOSE="sudo docker compose"
    print_status "Using: sudo docker compose"
else
    print_error "Neither docker-compose nor 'docker compose' are available"
    exit 1
fi

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    print_status "Creating .env file from template..."
    cp env.docker .env
    print_success ".env file created"
else
    print_warning ".env file already exists, skipping creation"
fi

# Stop any existing containers
print_status "Stopping existing containers..."
$DOCKER_COMPOSE down --remove-orphans

# Build and start containers
print_status "Building and starting containers..."
$DOCKER_COMPOSE up -d --build

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 10

# Check if Laravel project exists, if not create it
if [ ! -f composer.json ]; then
    print_status "Laravel project not found. Creating Laravel project..."
    
    # Create Laravel project in a temporary directory
    $DOCKER_COMPOSE exec app composer create-project laravel/laravel /tmp/laravel-app --prefer-dist --no-interaction
    
    # Move Laravel files to current directory (excluding our Docker files)
    print_status "Moving Laravel files to project directory..."
    $DOCKER_COMPOSE exec app sh -c "cd /tmp/laravel-app && find . -maxdepth 1 -not -name '.' -not -name '..' -exec cp -r {} /var/www/ \;"
    
    # Update environment file with Docker settings
    print_status "Configuring environment for Docker..."
    $DOCKER_COMPOSE exec app cp env.docker .env
    
    # Clean up temporary directory
    $DOCKER_COMPOSE exec app rm -rf /tmp/laravel-app
    
    print_success "Laravel project created"
else
    print_warning "Laravel project already exists, skipping creation"
fi

# Install dependencies
print_status "Installing PHP dependencies..."
$DOCKER_COMPOSE exec app composer install

# Generate application key
print_status "Generating application key..."
$DOCKER_COMPOSE exec app php artisan key:generate

# Run database migrations
print_status "Running database migrations..."
$DOCKER_COMPOSE exec app php artisan migrate

# Clear and cache configuration
print_status "Optimizing application..."
$DOCKER_COMPOSE exec app php artisan config:cache
$DOCKER_COMPOSE exec app php artisan route:cache
$DOCKER_COMPOSE exec app php artisan view:cache


print_success "Development environment is ready!"
echo ""
echo "🌐 Application URLs:"
echo "   Web Application: http://localhost:8080"
echo "   phpMyAdmin (Database): http://localhost:8081"
echo "   MailHog (Email): http://localhost:8025"
echo ""
echo "🗄️  Database Access:"
echo "   Host: localhost"
echo "   Port: 3306"
echo "   Database: latinad_db"
echo "   Username: latinad_user"
echo "   Password: latinad_password"
echo ""
echo "🔧 Redis Access:"
echo "   Host: localhost"
echo "   Port: 6379"
echo ""
echo "📊 phpMyAdmin Credentials:"
echo "   Username: latinad_user"
echo "   Password: latinad_password"
echo ""
echo "📝 Useful Commands:"
echo "   Stop services: $DOCKER_COMPOSE down"
echo "   View logs: $DOCKER_COMPOSE logs -f"
echo "   Access container: $DOCKER_COMPOSE exec app bash"
echo "   Run Artisan commands: $DOCKER_COMPOSE exec app php artisan [command]"
echo ""
print_success "Happy coding! 🎉"
