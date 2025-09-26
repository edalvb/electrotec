# Runs `composer install` inside the Docker Compose `app` service.
# Usage: .\scripts\composer-install.ps1

param()

Write-Host "Checking if 'app' container is running..."
$appId = (& docker compose ps -q app) 2>$null
if (-not $appId) {
  Write-Host "Starting app container..."
  & docker compose up -d app | Out-Null
}

Write-Host "Running 'composer install' inside the app container..."
exit (& docker compose exec -T app composer install)
