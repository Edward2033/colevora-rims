#!/bin/bash

# Docker Deployment Test Script for Colevora Restaurant ERP

echo "========================================"
echo "  Colevora Docker Deployment Test"
echo "========================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
echo "Checking Docker installation..."
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker is not installed${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker is installed${NC}"

# Check if Docker Compose is installed
echo "Checking Docker Compose..."
if ! command -v docker compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose is not installed${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker Compose is installed${NC}"

# Check if .env exists
echo "Checking environment file..."
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  .env not found, creating from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✅ .env file created${NC}"
else
    echo -e "${GREEN}✅ .env file exists${NC}"
fi

# Stop any running containers
echo ""
echo "Stopping any running containers..."
docker compose down

# Build Docker image
echo ""
echo "Building Docker image..."
echo -e "${YELLOW}This may take 5-10 minutes on first build...${NC}"
if docker compose build; then
    echo -e "${GREEN}✅ Docker image built successfully${NC}"
else
    echo -e "${RED}❌ Docker build failed${NC}"
    exit 1
fi

# Start containers
echo ""
echo "Starting containers..."
if docker compose up -d; then
    echo -e "${GREEN}✅ Containers started successfully${NC}"
else
    echo -e "${RED}❌ Failed to start containers${NC}"
    exit 1
fi

# Wait for containers to be ready
echo ""
echo "Waiting for application to be ready..."
sleep 10

# Check if containers are running
echo "Checking container status..."
if docker compose ps | grep -q "Up"; then
    echo -e "${GREEN}✅ Containers are running${NC}"
else
    echo -e "${RED}❌ Containers are not running${NC}"
    docker compose logs
    exit 1
fi

# Test HTTP connection
echo ""
echo "Testing HTTP connection..."
sleep 5
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
if [ "$HTTP_STATUS" -eq 200 ] || [ "$HTTP_STATUS" -eq 302 ]; then
    echo -e "${GREEN}✅ Application is responding (HTTP $HTTP_STATUS)${NC}"
else
    echo -e "${RED}❌ Application is not responding (HTTP $HTTP_STATUS)${NC}"
    echo "Showing application logs:"
    docker compose logs app
    exit 1
fi

# Show running containers
echo ""
echo "Running containers:"
docker compose ps

# Show logs
echo ""
echo "Recent application logs:"
docker compose logs --tail=20 app

# Final instructions
echo ""
echo "========================================"
echo -e "${GREEN}✅ Docker deployment test successful!${NC}"
echo "========================================"
echo ""
echo "Application is running at: http://localhost"
echo ""
echo "Default login credentials:"
echo "  Email: edwardcole203@gmail.com"
echo "  Password: password"
echo ""
echo "Useful commands:"
echo "  View logs:        docker compose logs -f"
echo "  Stop containers:  docker compose down"
echo "  Restart:          docker compose restart"
echo "  Shell access:     docker compose exec app bash"
echo ""
echo "To stop containers, run:"
echo "  docker compose down"
echo ""
