#!/bin/bash
# Diagnostic script to check Parent model issue on server

echo "========================================="
echo "Checking Parent Model Issue"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

echo ""
echo "1. Checking if old Parent.php exists..."
if [ -f "app/Models/Parent.php" ]; then
    echo "   ❌ ERROR: app/Models/Parent.php STILL EXISTS!"
    echo "   This file must be deleted!"
    ls -la app/Models/Parent.php
else
    echo "   ✓ Old Parent.php does not exist (good!)"
fi

echo ""
echo "2. Checking if ParentModel.php exists..."
if [ -f "app/Models/ParentModel.php" ]; then
    echo "   ✓ ParentModel.php exists"
    head -10 app/Models/ParentModel.php | grep -q "class ParentModel" && echo "   ✓ Class name is correct: ParentModel"
else
    echo "   ❌ ERROR: ParentModel.php does not exist!"
fi

echo ""
echo "3. Checking Composer autoload for old Parent class..."
if [ -f "vendor/composer/autoload_classmap.php" ]; then
    if grep -q "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "   ❌ ERROR: Old 'Parent' class found in autoload_classmap.php!"
        echo "   Found:"
        grep "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php
    else
        echo "   ✓ Old Parent class NOT in autoload (good!)"
    fi
    
    if grep -q "App\\\\Models\\\\ParentModel" vendor/composer/autoload_classmap.php 2>/dev/null; then
        echo "   ✓ ParentModel found in autoload"
    else
        echo "   ⚠ WARNING: ParentModel not found in autoload"
    fi
else
    echo "   ⚠ autoload_classmap.php not found"
fi

echo ""
echo "4. Checking for cached files..."
CACHE_COUNT=$(find bootstrap/cache -name "*.php" -type f 2>/dev/null | wc -l)
VIEW_COUNT=$(find storage/framework/views -name "*.php" -type f 2>/dev/null | wc -l)
echo "   Found $CACHE_COUNT files in bootstrap/cache/"
echo "   Found $VIEW_COUNT files in storage/framework/views/"

if [ "$CACHE_COUNT" -gt 0 ] || [ "$VIEW_COUNT" -gt 0 ]; then
    echo "   ⚠ Cache files exist - they may contain old Parent references"
fi

echo ""
echo "========================================="
echo "RECOMMENDED FIX:"
echo "========================================="
echo ""
echo "If you see errors above, run:"
echo "  ./fix-parent-final.sh"
echo ""
echo "Or manually run:"
echo "  rm -f app/Models/Parent.php"
echo "  rm -f vendor/composer/autoload_classmap.php"
echo "  rm -f vendor/composer/autoload_static.php"
echo "  composer dump-autoload --optimize"
echo "  rm -f bootstrap/cache/*.php"
echo "  rm -rf storage/framework/views/*"
echo ""
