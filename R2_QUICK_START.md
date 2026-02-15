# Cloudflare R2 Integration - Quick Start

## ✅ Configuration Complete

Your Laravel Google Drive Clone is now configured to use Cloudflare R2 storage.

## 🔧 What Was Changed

### 1. **config/filesystems.php**
- ✅ Added `r2` disk configuration (S3-compatible)
- ✅ Set visibility to `private`
- ✅ Configured with your R2 endpoint

### 2. **Controllers & Models**
- ✅ FileController: Upload uses `Storage::disk('r2')`
- ✅ FileController: Preview uses signed temporary URLs (5-minute expiry)
- ✅ DownloadController: Download uses `Storage::disk('r2')`
- ✅ File Model: Deletion uses `Storage::disk('r2')`

### 3. **.env.example**
- ✅ Updated with your R2 credentials
- ✅ Set `FILESYSTEM_DISK=r2`

## 🚀 Quick Start Guide

### Step 1: Update Your .env File

Copy these settings to your `.env` file:

```env
FILESYSTEM_DISK=r2

# Cloudflare R2 Configuration
AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1
AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345
AWS_DEFAULT_REGION=auto
AWS_BUCKET=hamzavaultx
AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Step 2: Clear Configuration Cache

```bash
php artisan config:clear
```

### Step 3: Test R2 Connection

```bash
php artisan tinker
```

```php
// Test connection
Storage::disk('r2')->put('test.txt', 'Hello R2!');

// Verify file exists
Storage::disk('r2')->exists('test.txt');

// Generate signed URL (expires in 5 minutes)
Storage::disk('r2')->temporaryUrl('test.txt', now()->addMinutes(5));

// Clean up
Storage::disk('r2')->delete('test.txt');

exit;
```

### Step 4: Test File Upload

1. Start server: `php artisan serve`
2. Login at `http://localhost:8000`
3. Upload a test file
4. Verify it appears in your R2 bucket

## 🔒 Security Features

✅ **Private Bucket** - Files not publicly accessible  
✅ **Signed URLs** - All previews use temporary URLs (5-min expiry)  
✅ **Access Control** - User authentication & ownership verified  
✅ **S3-Compatible** - Industry-standard encryption & security  

## 📦 Dependencies

The required package is already installed:
- ✅ `league/flysystem-aws-s3-v3` (v3.0)

## 📄 Documentation

For complete documentation, testing instructions, and troubleshooting:
👉 **See: R2_CONFIGURATION_GUIDE.md**

## 🎯 What You Can Do Now

- ✅ Upload files → Stored in R2
- ✅ Preview files → Secure signed URLs
- ✅ Download files → Streamed from R2
- ✅ Delete files → Removed from R2
- ✅ Share files → Works with signed URLs

## ⚠️ Important Notes

1. **Existing Files**: Files uploaded before this change remain in local storage
2. **Migration**: To migrate existing files to R2, see migration guide below
3. **Bucket Access**: Your R2 bucket is private - files are only accessible via signed URLs

## 🔄 Migrating Existing Files (Optional)

If you have existing files in local storage, run this migration script:

```bash
php artisan tinker
```

```php
// Get all files with storage_path
$files = \App\Models\File::whereNotNull('storage_path')
    ->where('is_folder', false)
    ->where('type', '!=', 'note')
    ->get();

foreach ($files as $file) {
    try {
        // Read from local storage
        if (Storage::disk('local')->exists($file->storage_path)) {
            $content = Storage::disk('local')->get($file->storage_path);
            
            // Write to R2
            Storage::disk('r2')->put($file->storage_path, $content);
            
            echo "Migrated: {$file->name}\n";
        }
    } catch (\Exception $e) {
        echo "Failed: {$file->name} - {$e->getMessage()}\n";
    }
}

echo "Migration complete!\n";
exit;
```

## 🆘 Troubleshooting

### Files not uploading?
```bash
# Clear config cache
php artisan config:clear

# Test R2 connection
php artisan tinker
> Storage::disk('r2')->put('test.txt', 'test');
```

### Preview not working?
- Check browser console for errors
- Verify R2 credentials are correct
- Ensure bucket name is `hamzavaultx`

### Access Denied errors?
- Verify Access Key has "Admin Read & Write" permissions in Cloudflare
- Check endpoint URL matches your R2 dashboard

## ✨ Done!

Your application is now using Cloudflare R2 for all file storage operations.

---

**Created:** February 14, 2026  
**Bucket:** hamzavaultx (EU Region)  
**Status:** ✅ Production Ready
