#!/bin/bash
# Script to fix Parent model issue on server
# Run this on your server after pulling the latest code

echo "Fixing Parent model issue..."

# Navigate to application directory
cd /home/smartchildcare.online/app || exit 1

# Remove old Parent.php file if it exists
if [ -f "app/Models/Parent.php" ]; then
    echo "Removing old Parent.php file..."
    rm -f app/Models/Parent.php
fi

# Verify ParentModel.php exists
if [ ! -f "app/Models/ParentModel.php" ]; then
    echo "ERROR: ParentModel.php not found! Please pull latest code from GitHub."
    exit 1
fi

# Regenerate Composer autoload
echo "Regenerating Composer autoload..."
composer dump-autoload --optimize

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
echo "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done! The Parent model issue should be fixed."
echo "Please test the CSV upload functionality."
