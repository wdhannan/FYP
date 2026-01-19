#!/bin/bash
# Fix Parent model issue on server (when not using git)
# Run this on your server: bash fix-parent-on-server.sh

echo "=== Fixing Parent Model Issue ==="

# Navigate to application directory
cd /home/smartchildcare.online/app || exit 1

echo "1. Checking for old Parent.php file..."
if [ -f "app/Models/Parent.php" ]; then
    echo "   Found old Parent.php - removing it..."
    rm -f app/Models/Parent.php
    echo "   ✓ Removed old Parent.php"
else
    echo "   ✓ No old Parent.php found"
fi

echo "2. Checking for ParentModel.php..."
if [ -f "app/Models/ParentModel.php" ]; then
    echo "   ✓ ParentModel.php exists"
else
    echo "   ✗ ERROR: ParentModel.php not found!"
    echo "   Please upload ParentModel.php to app/Models/ directory"
    exit 1
fi

echo "3. Regenerating Composer autoload (CRITICAL!)..."
composer dump-autoload --optimize
if [ $? -eq 0 ]; then
    echo "   ✓ Composer autoload regenerated"
else
    echo "   ✗ ERROR: Failed to regenerate autoload"
    exit 1
fi

echo "4. Clearing Laravel caches..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "   ✓ Caches cleared"

echo "5. Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✓ Caches rebuilt"

echo ""
echo "=== Fix Complete ==="
echo "The Parent model issue should now be resolved."
echo "Please test the CSV upload functionality."
