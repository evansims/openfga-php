#!/usr/bin/env bash

# Integration Test Runner with Guaranteed Cleanup
# Ensures cleanup happens even if tests fail or are interrupted

set -euo pipefail

COMPOSE_FILE="docker-compose.integration.yml"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_ROOT"

# Enable BuildKit for better caching
export DOCKER_BUILDKIT=1
export COMPOSE_DOCKER_CLI_BUILD=1

# Check for CI mode
CI_MODE="${CI:-false}"

# Trap to ensure cleanup on exit
cleanup() {
    local exit_code=$?
    echo ""
    echo "🧹 Running cleanup..."
    "$SCRIPT_DIR/integration-cleanup.sh"
    exit $exit_code
}

# Set trap for various signals
trap cleanup EXIT INT TERM

# Function to check if docker compose is available
check_docker_compose() {
    if command -v docker-compose &> /dev/null; then
        echo "docker-compose"
    elif docker compose version &> /dev/null; then
        echo "docker compose"
    else
        echo "❌ Docker Compose is not installed or not in PATH" >&2
        exit 1
    fi
}

DOCKER_COMPOSE=$(check_docker_compose)

if [ "$CI_MODE" = "true" ]; then
    echo "🚀 Starting integration tests (CI mode)..."
else
    echo "🚀 Starting integration tests..."
fi
echo ""

# Pull base images in parallel for faster startup (skip in CI for speed)
if [ "$CI_MODE" != "true" ]; then
    echo "📥 Pulling base images..."
    $DOCKER_COMPOSE -f "$COMPOSE_FILE" pull --parallel --quiet openfga otel-collector 2>/dev/null || true
fi

# Build the test container with cache
echo "🔨 Building test container..."
$DOCKER_COMPOSE -f "$COMPOSE_FILE" build \
    --build-arg BUILDKIT_INLINE_CACHE=1 \
    test

# Start all services at once (docker-compose will handle dependencies)
echo "🚀 Starting services..."
$DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d

# Wait for services using docker-compose's built-in health checks
echo "⏳ Waiting for services to be healthy..."
if [ "$CI_MODE" = "true" ]; then
    # In CI, just wait a fixed time for speed
    sleep 10
else
    # In local dev, wait for proper health checks
    timeout=60
    counter=0
    while [ $counter -lt $timeout ]; do
        if $DOCKER_COMPOSE -f "$COMPOSE_FILE" ps | grep -E "(unhealthy|starting)" > /dev/null; then
            sleep 2
            counter=$((counter + 2))
        else
            break
        fi
    done
fi

# Check service status
echo "📊 Service status:"
$DOCKER_COMPOSE -f "$COMPOSE_FILE" ps

# Run the tests
echo "🧪 Running integration tests..."
$DOCKER_COMPOSE -f "$COMPOSE_FILE" run --rm test

echo ""
echo "✅ Integration tests completed successfully!"