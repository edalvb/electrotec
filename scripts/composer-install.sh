#!/usr/bin/env bash
set -euo pipefail

echo "Checking if 'app' container is running..."
if ! docker compose ps -q app >/dev/null; then
  echo "Starting app container..."
  docker compose up -d app >/dev/null
fi

echo "Running 'composer install' inside the app container..."
docker compose exec -T app composer install
