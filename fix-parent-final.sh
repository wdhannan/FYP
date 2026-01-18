#!/bin/bash
# FINAL fix for Parent model issue
# This script aggressively removes all traces of the old Parent class

echo "========================================="
echo "FINAL FIX for Parent Model Issue"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

echo "Step 1: Pulling latest code..."
git pull origin main
echo "  ✓"

echo "Step 2: Removing old Parent.php file (CRITICAL)..."
rm -f app/Models/Parent.php
if [ -f "app/Models/Parent.php" ]; then
    echo "  ERROR: Failed to remove Parent.php - check permissions"
    exit 1
fi
echo "  ✓ Old file removed"

echo "Step 3: Verifying ParentModel.php exists..."
if [ ! -f "app/Models/ParentModel.php" ]; then
    echo "  ERROR: ParentModel.php not found!"
    exit 1
fi
echo "  ✓ ParentModel.php exists"

echo "Step 4: Deleting ALL cache files (aggressive cleanup)..."
# Delete bootstrap cache files (these may contain class references)
find bootstrap/cache -type f -name "*.php" -delete 2>/dev/null
rm -f bootstrap/cache/*.php 2>/dev/null
rm -f bootstrap/cache/routes-*.php 2>/dev/null
rm -f bootstrap/cache/config.php 2>/dev/null
rm -f bootstrap/cache/services.php 2>/dev/null
rm -f bootstrap/cache/packages.php 2>/dev/null

# Delete compiled views
find storage/framework/views -type f -name "*.php" -delete 2>/dev/null
rm -rf storage/framework/views/* 2>/dev/null

# Delete application cache
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -rf storage/framework/cache/* 2>/dev/null

echo "  ✓ All cache files deleted"

echo "Step 5: Regenerating Composer autoload (CRITICAL - this is the main fix)..."
# Remove composer autoload cache first
rm -f vendor/composer/autoload_classmap.php 2>/dev/null
rm -f vendor/composer/autoload_static.php 2>/dev/null

# Regenerate autoload
composer dump-autoload --optimize --no-interaction --quiet
if [ $? -ne 0 ]; then
    echo "  ERROR: composer dump-autoload failed"
    exit 1
fi
echo "  ✓ Composer autoload regenerated"

echo "Step 6: Verifying Parent class is not in autoload..."
if grep -q "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
    echo "  WARNING: Old Parent class still in autoload! Trying again..."
    rm -f vendor/composer/autoload_classmap.php
    composer dump-autoload --optimize --no-interaction --quiet
fi

if grep -q "App\\\\Models\\\\ParentModel" vendor/composer/autoload_classmap.php 2>/dev/null; then
    echo "  ✓ ParentModel found in autoload"
else
    echo "  WARNING: ParentModel not found in autoload"
fi

echo ""
echo "========================================="
echo "Fix completed!"
echo "========================================="
echo ""
echo "Actions taken:"
echo "  1. Removed old Parent.php file"
echo "  2. Deleted ALL cache files"
echo "  3. Regenerated Composer autoload"
echo ""
echo "The error 'Class App\\Models\\Parent not found' should now be fixed."
echo ""
echo "If you still see the error, try:"
echo "  1. Check if app/Models/Parent.php still exists: ls -la app/Models/Parent.php"
echo "  2. Verify ParentModel exists: ls -la app/Models/ParentModel.php"
echo "  3. Check composer autoload: grep -i parent vendor/composer/autoload_classmap.php"
echo ""
echo "Now test the CSV upload functionality."
echo ""
