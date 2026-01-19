#!/bin/bash
# Find what's trying to load App\Models\Parent

echo "========================================="
echo "Finding Parent Class Reference"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

echo ""
echo "1. Checking autoload for old Parent class..."
if [ -f "vendor/composer/autoload_classmap.php" ]; then
    echo "   Searching for 'App\\\\Models\\\\Parent' (without Model)..."
    if grep -i "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "   ❌ FOUND: Old Parent class in autoload!"
    else
        echo "   ✓ Not found in autoload (good)"
    fi
    
    echo ""
    echo "   All Parent-related entries in autoload:"
    grep -i "parent" vendor/composer/autoload_classmap.php | head -5
fi

echo ""
echo "2. Checking for old Parent.php file..."
if [ -f "app/Models/Parent.php" ]; then
    echo "   ❌ ERROR: app/Models/Parent.php EXISTS!"
    echo "   First 10 lines:"
    head -10 app/Models/Parent.php
else
    echo "   ✓ app/Models/Parent.php does not exist (good)"
fi

echo ""
echo "3. Checking ParentModel.php..."
if [ -f "app/Models/ParentModel.php" ]; then
    echo "   ✓ ParentModel.php exists"
    echo "   Class name check:"
    head -10 app/Models/ParentModel.php | grep "class "
else
    echo "   ❌ ERROR: ParentModel.php does not exist!"
fi

echo ""
echo "4. Checking Laravel logs for error details..."
if [ -f "storage/logs/laravel.log" ]; then
    echo "   Last 20 lines of Laravel log:"
    tail -20 storage/logs/laravel.log | grep -i "parent" || echo "   No Parent-related errors in recent logs"
else
    echo "   ⚠ Laravel log not found"
fi

echo ""
echo "5. Checking for cached route/config files..."
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo "   Checking routes cache for Parent reference..."
    grep -i "App\\\\Models\\\\Parent[^M]" bootstrap/cache/routes-v7.php 2>/dev/null && echo "   ❌ Found in routes cache!" || echo "   ✓ Not in routes cache"
fi

if [ -f "bootstrap/cache/config.php" ]; then
    echo "   Checking config cache for Parent reference..."
    grep -i "App\\\\Models\\\\Parent[^M]" bootstrap/cache/config.php 2>/dev/null && echo "   ❌ Found in config cache!" || echo "   ✓ Not in config cache"
fi

echo ""
echo "========================================="
echo "RECOMMENDED FIX:"
echo "========================================="
echo ""
echo "Run these commands:"
echo ""
echo "cd /home/smartchildcare.online/app"
echo "rm -f vendor/composer/autoload_classmap.php"
echo "rm -f vendor/composer/autoload_static.php"
echo "composer dump-autoload --optimize --no-interaction"
echo "php -r \"if (function_exists('opcache_reset')) { opcache_reset(); }\""
echo "systemctl restart lsws"
echo ""
