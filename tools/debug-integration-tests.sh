#!/usr/bin/env bash

# Debug script for integration tests
set -euo pipefail

COMPOSE_FILE="docker-compose.integration.yml"

echo "🔍 Debugging integration test setup..."
echo ""

# Clean up any existing containers
echo "🧹 Cleaning up existing containers..."
docker compose -f "$COMPOSE_FILE" down -v --remove-orphans || true

echo ""
echo "🚀 Starting services..."
docker compose -f "$COMPOSE_FILE" up -d openfga otel-collector

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 5

echo ""
echo "📊 Service status:"
docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "🔍 Checking OpenFGA health:"
curl -v http://localhost:8080/healthz || echo "OpenFGA health check failed"

echo ""
echo "🔍 Checking OTEL Collector health:"
curl -v http://localhost:8889/metrics | head -20 || echo "OTEL Collector health check failed"

echo ""
echo "📋 Container logs:"
echo "--- OpenFGA logs ---"
docker logs openfga-integration-tests --tail=20

echo ""
echo "--- OTEL Collector logs ---"
docker logs otel-collector-integration-tests --tail=20

echo ""
echo "🧪 Running test container interactively..."
docker compose -f "$COMPOSE_FILE" run --rm test sh -c '
echo "Inside test container..."
echo "Checking environment:"
env | grep -E "(FGA|OTEL)" | sort
echo ""
echo "Checking connectivity to OpenFGA:"
curl -v http://openfga:8080/healthz || echo "Cannot reach OpenFGA from test container"
echo ""
echo "Checking connectivity to OTEL Collector:"
curl -v http://otel-collector:8889/metrics | head -5 || echo "Cannot reach OTEL from test container"
echo ""
echo "Checking vendor directory:"
ls -la vendor/ | head -10 || echo "Vendor directory not found"
echo ""
echo "Running a simple test:"
./vendor/bin/pest --version || echo "Pest not found"
'

echo ""
echo "🧹 Cleaning up..."
docker compose -f "$COMPOSE_FILE" down -v --remove-orphans

echo ""
echo "✅ Debug complete!"