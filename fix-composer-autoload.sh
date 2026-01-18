#!/bin/bash
# Fix Composer autoload cache issue

echo "========================================="
echo "Fixing Composer Autoload Cache"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

echo ""
echo "Step 1: Checking current autoload status..."
if [ -f "vendor/composer/autoload_classmap.php" ]; then
    echo "  Checking for old Parent class in autoload..."
    if grep -q "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "  ❌ Found old 'Parent' class in autoload!"
        grep "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php
    else
        echo "  ✓ Old Parent class NOT found in autoload"
    fi
    
    if grep -q "App\\\\Models\\\\ParentModel" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "  ✓ ParentModel found in autoload"
    else
        echo "  ⚠ ParentModel NOT found in autoload"
    fi
else
    echo "  ⚠ autoload_classmap.php not found"
fi

echo ""
echo "Step 2: Deleting Composer autoload cache files..."
rm -f vendor/composer/autoload_classmap.php
rm -f vendor/composer/autoload_static.php
rm -f vendor/composer/autoload_psr4.php
rm -f vendor/composer/autoload_real.php
echo "  ✓ Deleted autoload cache files"

echo ""
echo "Step 3: Regenerating Composer autoload..."
composer dump-autoload --optimize --no-interaction
if [ $? -eq 0 ]; then
    echo "  ✓ Composer autoload regenerated successfully"
else
    echo "  ❌ ERROR: composer dump-autoload failed"
    exit 1
fi

echo ""
echo "Step 4: Verifying fix..."
if [ -f "vendor/composer/autoload_classmap.php" ]; then
    if grep -q "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "  ❌ ERROR: Old 'Parent' class STILL in autoload!"
        echo "  This shouldn't happen. Please check manually:"
        echo "  grep -i parent vendor/composer/autoload_classmap.php"
    else
        echo "  ✓ Old Parent class NOT in autoload (fixed!)"
    fi
    
    if grep -q "App\\\\Models\\\\ParentModel" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "  ✓ ParentModel found in autoload (good!)"
    else
        echo "  ⚠ WARNING: ParentModel not found in autoload"
    fi
fi

echo ""
echo "Step 5: Clearing Laravel cache files..."
rm -f bootstrap/cache/*.php 2>/dev/null
rm -rf storage/framework/views/* 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
echo "  ✓ Laravel cache cleared"

echo ""
echo "========================================="
echo "Fix completed!"
echo "========================================="
echo ""
echo "The Composer autoload has been regenerated."
echo "Please test the CSV upload functionality now."
echo ""
