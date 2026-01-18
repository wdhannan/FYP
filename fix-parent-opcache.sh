#!/bin/bash
# Complete fix including PHP OpCache clearing

echo "========================================="
echo "COMPLETE FIX for Parent Model Error"
echo "========================================="

cd /home/smartchildcare.online/app || exit 1

echo ""
echo "Step 1: Verify ParentModel.php exists..."
if [ ! -f "app/Models/ParentModel.php" ]; then
    echo "  ❌ ERROR: ParentModel.php not found! Creating it..."
    cat > app/Models/ParentModel.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parent';
    protected $primaryKey = 'ParentID';
    public $timestamps = false;

    protected $fillable = [
        'ParentID',
        'MotherName',
        'MphoneNumber',
        'MEmail',
        'MIdentificationNumber',
        'FatherName',
        'FPhoneNumber',
        'FEmail',
        'FIdentificationNumber',
    ];
}
EOF
    echo "  ✓ File created"
else
    echo "  ✓ File exists"
fi

echo ""
echo "Step 2: Delete Composer autoload cache..."
rm -f vendor/composer/autoload_classmap.php
rm -f vendor/composer/autoload_static.php
rm -f vendor/composer/autoload_psr4.php
echo "  ✓ Cache deleted"

echo ""
echo "Step 3: Regenerate Composer autoload..."
composer dump-autoload --optimize --no-interaction
echo "  ✓ Autoload regenerated"

echo ""
echo "Step 4: Delete ALL Laravel cache files..."
find bootstrap/cache -name "*.php" -type f -delete 2>/dev/null
find storage/framework/views -name "*.php" -type f -delete 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -rf storage/framework/sessions/* 2>/dev/null
echo "  ✓ Laravel cache cleared"

echo ""
echo "Step 5: Clear PHP OpCache (CRITICAL for this error)..."
php -r "
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo '  ✓ OpCache cleared\n';
} else {
    echo '  ⚠ OpCache not enabled\n';
}
"

echo ""
echo "Step 6: Restart web server (LiteSpeed)..."
systemctl restart lsws 2>/dev/null || service lsws restart 2>/dev/null || echo "  ⚠ Could not restart server (may need manual restart)"

echo ""
echo "Step 7: Verify ParentModel in autoload..."
if grep -q "App\\\\Models\\\\ParentModel" vendor/composer/autoload_classmap.php 2>/dev/null; then
    echo "  ✓ ParentModel found in autoload"
else
    echo "  ❌ ERROR: ParentModel NOT in autoload!"
fi

echo ""
echo "Step 8: Check for old Parent class in autoload..."
if grep -q "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php 2>/dev/null; then
    echo "  ❌ ERROR: Old Parent class still in autoload!"
    grep "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php
else
    echo "  ✓ Old Parent class NOT in autoload (good!)"
fi

echo ""
echo "========================================="
echo "Fix completed!"
echo "========================================="
echo ""
echo "If error persists, try:"
echo "  1. Wait 30 seconds for OpCache to clear"
echo "  2. Hard refresh your browser (Ctrl+F5)"
echo "  3. Check Laravel logs: tail -f storage/logs/laravel.log"
echo ""
