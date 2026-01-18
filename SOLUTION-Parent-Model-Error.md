# SOLUTION: "Class App\Models\Parent not found" Error

## What this error means:
The application is trying to load a class called `App\Models\Parent`, but this class doesn't exist because:
1. It was renamed to `ParentModel` (because "Parent" is a reserved keyword in PHP)
2. The `ParentModel.php` file might be missing on your server
3. Cached files might still reference the old class name

## Complete Fix (Run these commands on your server):

```bash
cd /home/smartchildcare.online/app

# Step 1: Create ParentModel.php file (if it doesn't exist)
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

# Step 2: Verify file was created
ls -la app/Models/ParentModel.php

# Step 3: Delete ALL Composer autoload cache
rm -f vendor/composer/autoload_classmap.php
rm -f vendor/composer/autoload_static.php
rm -f vendor/composer/autoload_psr4.php

# Step 4: Regenerate Composer autoload (CRITICAL - this tells PHP about ParentModel)
composer dump-autoload --optimize --no-interaction

# Step 5: Verify ParentModel is now in autoload
grep -i "ParentModel" vendor/composer/autoload_classmap.php

# Step 6: Delete ALL Laravel cache files (may contain old Parent references)
find bootstrap/cache -name "*.php" -type f -delete 2>/dev/null
find storage/framework/views -name "*.php" -type f -delete 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null

# Step 7: Clear PHP OpCache (if available)
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OpCache cleared\n'; }"
```

## Verify the fix worked:

```bash
# Check file exists
ls -la app/Models/ParentModel.php

# Check ParentModel is in autoload
grep -i "ParentModel" vendor/composer/autoload_classmap.php

# Check old Parent is NOT in autoload
grep -i "App\\\\Models\\\\Parent[^M]" vendor/composer/autoload_classmap.php
# (Should return nothing if fixed)
```

After running these commands, try uploading the CSV file again. The error should be resolved.
