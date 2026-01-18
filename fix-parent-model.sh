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

# Step 4: Remove all Laravel cache files
echo "Step 4: Removing Laravel cache files..."
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
rm -rf storage/framework/sessions/*
echo "  -> Cache files removed"

# Step 5: Regenerate Composer autoload (CRITICAL)
echo "Step 5: Regenerating Composer autoload..."
composer dump-autoload --optimize --no-interaction
echo "  -> Composer autoload regenerated ✓"

# Step 6: Clear all Laravel caches
echo "Step 6: Clearing Laravel caches..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
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
