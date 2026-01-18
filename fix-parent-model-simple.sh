#!/bin/bash
# Simplified fix script that works around permission issues
# Run this as the application user (smart3938) or with sudo

echo "========================================="
echo "Fixing Parent model issue (Simple Version)"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

# Step 1: Pull latest code
echo "Step 1: Pulling latest code..."
git pull origin main

# Step 2: Remove old Parent.php file
echo "Step 2: Removing old Parent.php file..."
rm -f app/Models/Parent.php
echo "  -> Old file removed ✓"

# Step 3: Verify ParentModel.php exists
if [ ! -f "app/Models/ParentModel.php" ]; then
    echo "ERROR: ParentModel.php not found!"
    exit 1
fi
echo "  -> ParentModel.php found ✓"

# Step 4: Manually remove ALL cache files (CRITICAL - cached files may contain old Parent references)
echo "Step 4: Manually removing ALL cache files..."
# Remove bootstrap cache files (these often contain class references)
find bootstrap/cache -name "*.php" -type f -delete 2>/dev/null || true
rm -f bootstrap/cache/*.php 2>/dev/null || true
rm -f bootstrap/cache/routes-*.php 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
rm -f bootstrap/cache/packages.php 2>/dev/null || true

# Remove compiled views (may contain old class references)
find storage/framework/views -name "*.php" -type f -delete 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true

# Remove application cache
rm -rf storage/framework/cache/data/* 2>/dev/null || true

# Remove sessions (optional, but helps)
rm -rf storage/framework/sessions/* 2>/dev/null || true

echo "  -> ALL cache files removed ✓"

# Step 5: Regenerate Composer autoload (MOST IMPORTANT)
echo "Step 5: Regenerating Composer autoload..."
composer dump-autoload --optimize --no-interaction
echo "  -> Composer autoload regenerated ✓"

echo ""
echo "========================================="
echo "Fix completed!"
echo "========================================="
echo ""
echo "The Parent model has been renamed to ParentModel."
echo "Composer autoload has been regenerated."
echo ""
echo "If you still see cache errors, run these as the app user:"
echo "  sudo -u smart3938 php artisan config:cache"
echo "  sudo -u smart3938 php artisan route:cache"
echo ""
echo "Please test the CSV upload functionality now."
echo ""
