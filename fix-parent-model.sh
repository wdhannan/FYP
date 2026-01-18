#!/bin/bash
# Script to fix Parent model issue on server
# Run this on your server after pulling the latest code

echo "========================================="
echo "Fixing Parent model issue..."
echo "========================================="

# Navigate to application directory
cd /home/smartchildcare.online/app || exit 1

# Step 1: Pull latest code
echo "Step 1: Pulling latest code from GitHub..."
git pull origin main

# Step 2: Remove old Parent.php file if it exists (CRITICAL)
echo "Step 2: Removing old Parent.php file..."
if [ -f "app/Models/Parent.php" ]; then
    echo "  -> Found old Parent.php, removing it..."
    rm -f app/Models/Parent.php
    echo "  -> Old file removed successfully"
else
    echo "  -> Old Parent.php not found (good!)"
fi

# Step 3: Verify ParentModel.php exists
echo "Step 3: Verifying ParentModel.php exists..."
if [ ! -f "app/Models/ParentModel.php" ]; then
    echo "ERROR: ParentModel.php not found! Please check your git pull."
    exit 1
else
    echo "  -> ParentModel.php found ✓"
fi

# Step 4: Fix permissions and create necessary directories
echo "Step 4: Setting up cache directories and fixing permissions..."

# Get the application owner (usually smart3938 based on previous commands)
APP_OWNER=$(stat -c '%U' . 2>/dev/null || echo "smart3938")

echo "  -> Application owner: $APP_OWNER"

# Create directories if they don't exist
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache
mkdir -p storage/logs

# Set ownership and permissions
chown -R $APP_OWNER:$APP_OWNER storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage/framework bootstrap/cache 2>/dev/null || true

echo "  -> Cache directories created/verified with proper permissions"

echo "Step 4b: Removing Laravel cache files..."
# Remove cache files (might fail due to permissions, that's OK)
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
echo "  -> Cache files removed (if accessible)"

# Step 5: Regenerate Composer autoload (CRITICAL)
echo "Step 5: Regenerating Composer autoload..."
composer dump-autoload --optimize --no-interaction
echo "  -> Composer autoload regenerated ✓"

# Step 6: Clear all Laravel caches
echo "Step 6: Clearing Laravel caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true
php artisan optimize:clear || true
echo "  -> All caches cleared ✓"

# Step 7: Rebuild optimized caches
echo "Step 7: Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "  -> Caches rebuilt ✓"

echo ""
echo "========================================="
echo "Fix completed successfully!"
echo "========================================="
echo ""
echo "The Parent model has been renamed to ParentModel."
echo "All caches have been cleared and regenerated."
echo ""
echo "Please test the CSV upload functionality now."
echo ""
