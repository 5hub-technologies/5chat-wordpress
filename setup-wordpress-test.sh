#!/bin/bash

# 5chat WordPress Plugin - Local Testing Environment Setup

echo "🚀 Setting up WordPress testing environment for 5chat plugin..."

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        echo "❌ Docker is not running. Please start Docker and try again."
        exit 1
    fi
    echo "✅ Docker is running"
}

# Function to start WordPress environment
start_wordpress() {
    echo "📦 Starting WordPress containers..."
    docker-compose up -d
    
    echo "⏳ Waiting for WordPress to be ready..."
    sleep 30
    
    echo "🎉 WordPress testing environment is ready!"
    echo ""
    echo "🌐 Access your WordPress site:"
    echo "   WordPress:  http://localhost:8080"
    echo "   phpMyAdmin: http://localhost:8081"
    echo ""
    echo "📋 Database credentials:"
    echo "   Database: wordpress"
    echo "   Username: wordpress"
    echo "   Password: wordpress"
    echo ""
    echo "🔧 WordPress Admin Setup:"
    echo "   1. Go to http://localhost:8080"
    echo "   2. Complete the WordPress installation"
    echo "   3. Go to Plugins → Installed Plugins"
    echo "   4. Activate '5chat Live Chat' plugin"
    echo "   5. Go to Settings → 5chat to test the plugin"
    echo ""
}

# Function to stop WordPress environment
stop_wordpress() {
    echo "🛑 Stopping WordPress containers..."
    docker-compose down
    echo "✅ WordPress environment stopped"
}

# Function to reset WordPress environment
reset_wordpress() {
    echo "🗑️  Resetting WordPress environment (this will delete all data)..."
    read -p "Are you sure? This will delete all WordPress data and database content. (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker-compose down -v
        docker-compose up -d
        echo "✅ WordPress environment reset"
    else
        echo "❌ Reset cancelled"
    fi
}

# Function to show logs
show_logs() {
    echo "📄 Showing WordPress logs..."
    docker-compose logs -f wordpress
}

# Function to show status
show_status() {
    echo "📊 WordPress environment status:"
    docker-compose ps
}

# Main menu
case "$1" in
    start)
        check_docker
        start_wordpress
        ;;
    stop)
        stop_wordpress
        ;;
    restart)
        check_docker
        stop_wordpress
        start_wordpress
        ;;
    reset)
        check_docker
        reset_wordpress
        ;;
    logs)
        show_logs
        ;;
    status)
        show_status
        ;;
    *)
        echo "5chat WordPress Plugin - Testing Environment Manager"
        echo ""
        echo "Usage: $0 {start|stop|restart|reset|logs|status}"
        echo ""
        echo "Commands:"
        echo "  start   - Start WordPress testing environment"
        echo "  stop    - Stop WordPress testing environment"
        echo "  restart - Restart WordPress testing environment"
        echo "  reset   - Reset environment (deletes all data)"
        echo "  logs    - Show WordPress container logs"
        echo "  status  - Show container status"
        echo ""
        echo "Quick start:"
        echo "  ./setup-wordpress-test.sh start"
        echo "  Then visit: http://localhost:8080"
        exit 1
        ;;
esac 