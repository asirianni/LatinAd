# LatinAd Challenge - Development Commands
# Makefile for common development tasks

# Determine Docker Compose command
ifeq ($(shell which docker-compose),)
    DOCKER_COMPOSE := sudo docker compose
else
    DOCKER_COMPOSE := docker-compose
endif

.PHONY: help start stop restart logs shell composer artisan test clean

# Default target
help: ## Show this help message
	@echo "LatinAd Challenge - Development Commands"
	@echo "========================================"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Environment management
start: ## Start the development environment
	@echo "🚀 Starting LatinAd development environment..."
	./start.sh

stop: ## Stop all services
	@echo "🛑 Stopping all services..."
	$(DOCKER_COMPOSE) down

restart: stop start ## Restart all services

logs: ## Show logs from all services
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Show logs from Laravel application
	$(DOCKER_COMPOSE) logs -f app

logs-web: ## Show logs from Nginx web server
	$(DOCKER_COMPOSE) logs -f web

logs-db: ## Show logs from MySQL database
	$(DOCKER_COMPOSE) logs -f db

logs-redis: ## Show logs from Redis cache
	$(DOCKER_COMPOSE) logs -f redis

# Container access
shell: ## Access Laravel application container shell
	$(DOCKER_COMPOSE) exec app bash

shell-root: ## Access Laravel application container as root
	$(DOCKER_COMPOSE) exec --user root app bash

shell-db: ## Access MySQL database shell
	$(DOCKER_COMPOSE) exec db mysql -u latinad_user -platinad_password latinad_db

# Laravel commands
composer: ## Run Composer commands (usage: make composer ARGS="install")
	$(DOCKER_COMPOSE) exec app composer $(ARGS)

artisan: ## Run Artisan commands (usage: make artisan ARGS="migrate")
	$(DOCKER_COMPOSE) exec app php artisan $(ARGS)

# Database operations
migrate: ## Run database migrations
	$(DOCKER_COMPOSE) exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	$(DOCKER_COMPOSE) exec app php artisan migrate:fresh --seed

migrate-rollback: ## Rollback last migration
	$(DOCKER_COMPOSE) exec app php artisan migrate:rollback

seed: ## Run database seeders
	$(DOCKER_COMPOSE) exec app php artisan db:seed

# Cache operations
cache-clear: ## Clear all caches
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear

cache-warm: ## Warm up caches
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache

# Testing
test: ## Run PHPUnit tests
	$(DOCKER_COMPOSE) exec app php artisan test

test-coverage: ## Run tests with coverage
	$(DOCKER_COMPOSE) exec app php artisan test --coverage


# Maintenance
clean: ## Clean up containers and volumes
	$(DOCKER_COMPOSE) down -v --remove-orphans
	sudo docker system prune -f

clean-all: ## Clean up everything including images
	$(DOCKER_COMPOSE) down -v --remove-orphans
	sudo docker system prune -a -f

# Status
status: ## Show status of all services
	$(DOCKER_COMPOSE) ps

health: ## Check health of all services
	@echo "🔍 Checking service health..."
	@echo ""
	@echo "📊 Container Status:"
	$(DOCKER_COMPOSE) ps
	@echo ""
	@echo "🌐 Web Application:"
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:8080 || echo "❌ Web application not responding"
	@echo ""
	@echo "🗄️  Database:"
	@$(DOCKER_COMPOSE) exec -T db mysql -u latinad_user -platinad_password -e "SELECT 1;" latinad_db 2>/dev/null && echo "✅ Database connection OK" || echo "❌ Database connection failed"
	@echo ""
	@echo "🔴 Redis:"
	@$(DOCKER_COMPOSE) exec -T redis redis-cli ping 2>/dev/null && echo "✅ Redis connection OK" || echo "❌ Redis connection failed"

# DDEV integration
ddev-start: ## Start using DDEV
	ddev start

ddev-stop: ## Stop DDEV
	ddev stop

ddev-restart: ## Restart DDEV
	ddev restart

ddev-logs: ## Show DDEV logs
	ddev logs

ddev-shell: ## Access DDEV shell
	ddev ssh

# Quick development workflow
dev-setup: ## Complete development setup (first time only)
	@echo "🔧 Setting up development environment..."
	./start.sh
	@echo "✅ Development environment ready!"

quick-start: ## Quick start for daily development
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	@echo "✅ Ready for development!"
