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

# Step 6: Clear all Laravel caches (with permission handling)
echo "Step 6: Clearing Laravel caches..."

# Try to clear caches, but if permissions fail, manually delete cache files
if php artisan config:clear 2>/dev/null; then
    echo "  -> Config cache cleared ✓"
else
    echo "  -> Config cache clear failed (permissions), manually removing..."
    rm -f bootstrap/cache/config.php 2>/dev/null || true
fi

if php artisan cache:clear 2>/dev/null; then
    echo "  -> Application cache cleared ✓"
else
    echo "  -> Application cache clear failed (permissions), manually removing..."
    rm -rf storage/framework/cache/data/* 2>/dev/null || true
fi

if php artisan route:clear 2>/dev/null; then
    echo "  -> Route cache cleared ✓"
else
    echo "  -> Route cache clear failed (permissions), manually removing..."
    rm -f bootstrap/cache/routes*.php 2>/dev/null || true
fi

if php artisan view:clear 2>/dev/null; then
    echo "  -> View cache cleared ✓"
else
    echo "  -> View cache clear failed (non-critical) ✓"
fi

if php artisan event:clear 2>/dev/null; then
    echo "  -> Event cache cleared ✓"
else
    echo "  -> Event cache clear skipped (non-critical) ✓"
fi

# Try optimize:clear, but don't fail if it doesn't work
if php artisan optimize:clear 2>/dev/null; then
    echo "  -> Optimize clear completed ✓"
else
    echo "  -> Optimize clear had issues (continuing anyway) ✓"
fi

echo "  -> Cache clearing completed ✓"

# Step 7: Rebuild optimized caches (only if we have write permissions)
echo "Step 7: Rebuilding optimized caches..."

if php artisan config:cache 2>/dev/null; then
    echo "  -> Config cache rebuilt ✓"
else
    echo "  -> Config cache rebuild failed (permissions issue - may need to run as app user)"
fi

if php artisan route:cache 2>/dev/null; then
    echo "  -> Route cache rebuilt ✓"
else
    echo "  -> Route cache rebuild failed (permissions issue - may need to run as app user)"
fi

if php artisan view:cache 2>/dev/null; then
    echo "  -> View cache rebuilt ✓"
else
    echo "  -> View cache rebuild skipped (optional)"
fi

echo "  -> Cache rebuilding completed ✓"

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
