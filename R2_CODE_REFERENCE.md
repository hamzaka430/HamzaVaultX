# Cloudflare R2 Code Reference

This document shows the exact code implementations for R2 integration.

---

## 1. Filesystem Disk Configuration

**File:** `config/filesystems.php`

```php
'r2' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'auto'),
    'bucket' => env('AWS_BUCKET'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => false,
    'visibility' => 'private',
],
```

---

## 2. File Upload

**File:** `app/Http/Controllers/FileController.php`  
**Method:** `saveFile()`

```php
public function saveFile($file, $parent, $user)
{
    // Store file in R2 bucket under user's directory
    $path = $file->store('/files/'.$user->id, 'r2');

    $model = new File();
    $model->is_folder = false;
    $model->storage_path = $path;
    $model->name = $file->getClientOriginalName();
    $model->mime = $file->getMimeType();
    $model->size = $file->getSize();
    $parent->appendNode($model);
}
```

**Usage:**
- Automatically called when users upload files via the UI
- Files are stored in `/files/{user_id}/` directory in R2
- Path stored in database for future retrieval

---

## 3. File Preview (Signed URLs)

**File:** `app/Http/Controllers/FileController.php`  
**Method:** `previewFile()`

```php
public function previewFile(File $file)
{
    // Check if user has access (owner or shared with them)
    $hasAccess = $file->isOwnedBy(auth()->id()) || 
                 FileShare::where('file_id', $file->id)
                          ->where('user_id', auth()->id())
                          ->exists();

    if (!$hasAccess) {
        abort(403, 'Unauthorized action.');
    }

    // If it's a note, return the content
    if ($file->type === 'note') {
        return response()->json([
            'type' => 'note',
            'name' => $file->name,
            'content' => $file->note_content ?? '',
        ]);
    }

    // For regular files, generate temporary signed URL (5 minutes)
    if ($file->storage_path) {
        try {
            $url = Storage::disk('r2')->temporaryUrl(
                $file->storage_path,
                now()->addMinutes(5)
            );

            return response()->json([
                'type' => 'file',
                'name' => $file->name,
                'mime' => $file->mime,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            // Fallback for local storage (doesn't support temporaryUrl)
            return response()->json([
                'type' => 'file',
                'name' => $file->name,
                'mime' => $file->mime,
                'url' => Storage::disk('r2')->url($file->storage_path),
            ]);
        }
    }

    abort(404, 'File not found.');
}
```

**Key Features:**
- ✅ Generates signed URLs valid for 5 minutes
- ✅ Checks user access permissions
- ✅ Handles both notes and files
- ✅ Fallback for non-S3 storage

---

## 4. Single File Download

**File:** `app/Http/Controllers/DownloadController.php`  
**Method:** `streamSingleFile()`

```php
private function streamSingleFile(File $file)
{
    // Handle note files
    if ($file->type === 'note') {
        $filename = $file->name . '.txt';
        $content = $file->note_content ?? '';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    // Download file from R2
    return Storage::disk('r2')->download($file->storage_path, $file->name);
}
```

**Usage:**
- Called when user downloads a single file
- Streams directly from R2 to user's browser
- Preserves original filename

---

## 5. Multiple Files Download (ZIP)

**File:** `app/Http/Controllers/DownloadController.php`  
**Method:** `addFilesToZip()`

```php
private function addFilesToZip($zip, $files, $ancestors = '')
{
    $tempFiles = [];
    
    foreach ($files as $file) {
        if ($file->is_folder) {
            // Recursively add folder contents
            $this->addFilesToZip($zip, $file->children, $ancestors.$file->name.'/');
        } else {
            // Handle notes differently
            if ($file->type === 'note') {
                $zip->addFromString($ancestors.$file->name.'.txt', $file->note_content ?? '');
            } else {
                // Stream from R2 storage into a local temp file for zipping
                $tempFile = tempnam(sys_get_temp_dir(), 'dl_');
                file_put_contents($tempFile, Storage::disk('r2')->get($file->storage_path));
                $zip->addFile($tempFile, $ancestors.$file->name);
                $tempFiles[] = $tempFile;
            }
        }
    }
    
    // Clean up temp files after they're added to zip
    register_shutdown_function(function () use ($tempFiles) {
        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    });
}
```

**Key Features:**
- ✅ Downloads files from R2 to temporary local files
- ✅ Packages into ZIP archive
- ✅ Cleans up temporary files after sending
- ✅ Handles nested folder structures

---

## 6. File Deletion

**File:** `app/Models/File.php`  
**Method:** `deleteForever()`

```php
public function deleteForever()
{
    // If it's a folder, recursively delete all children first (including trashed ones)
    if ($this->is_folder) {
        $children = static::withTrashed()->where('parent_id', $this->id)->get();
        foreach ($children as $child) {
            $child->deleteForever();
        }
    }
    
    // Delete related records to avoid foreign key constraint errors
    \DB::table('file_shares')->where('file_id', $this->id)->delete();
    \DB::table('starred_files')->where('file_id', $this->id)->delete();
    
    // Delete files from R2 storage (only for actual files, not folders or notes)
    if (!$this->is_folder && $this->type !== 'note' && $this->storage_path) {
        \Illuminate\Support\Facades\Storage::disk('r2')->delete($this->storage_path);
    }
    
    // Force delete the database record
    $this->forceDelete();
}
```

**File:** `app/Models/File.php`  
**Method:** `deleteFilesFromStorage()`

```php
public function deleteFilesFromStorage($files)
{
    foreach ($files as $file) {
        if ($file->is_folder) {
            // Recursively delete folder contents
            $this->deleteFilesFromStorage($file->children);
        } else {
            // Only delete from storage if it's not a note
            if ($file->type !== 'note' && $file->storage_path) {
                Storage::disk('r2')->delete($file->storage_path);
            }
        }
    }
}
```

**Key Features:**
- ✅ Deletes files from R2 when permanently deleted
- ✅ Handles folder recursion
- ✅ Cleans up database relationships
- ✅ Skips deletion for notes (stored in database)

---

## 7. Environment Configuration

**File:** `.env`

```env
# Set R2 as default filesystem
FILESYSTEM_DISK=r2

# Cloudflare R2 Configuration
AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1
AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345
AWS_DEFAULT_REGION=auto
AWS_BUCKET=hamzavaultx
AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 8. Testing Code

### Test 1: Basic R2 Connection

```php
// In Tinker: php artisan tinker

use Illuminate\Support\Facades\Storage;

// Upload test file
Storage::disk('r2')->put('test.txt', 'Hello from R2!');

// Check if exists
Storage::disk('r2')->exists('test.txt'); // true

// Read content
Storage::disk('r2')->get('test.txt'); // "Hello from R2!"

// Generate signed URL (5 minutes)
Storage::disk('r2')->temporaryUrl('test.txt', now()->addMinutes(5));
// Returns: "https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com/test.txt?X-Amz-..."

// Delete test file
Storage::disk('r2')->delete('test.txt');
```

### Test 2: Upload via Controller

```php
// In Tinker: php artisan tinker

$user = \App\Models\User::first();
$parent = \App\Models\File::where('created_by', $user->id)->whereIsRoot()->first();

// Simulate file upload (for testing purposes)
// In real app, this happens via form upload in browser
```

### Test 3: Generate Preview URL

```php
// In Tinker: php artisan tinker

// Find a file
$file = \App\Models\File::where('is_folder', false)
    ->where('type', '!=', 'note')
    ->first();

// Generate preview URL
$url = Storage::disk('r2')->temporaryUrl($file->storage_path, now()->addMinutes(5));

echo $url;
// Copy and paste this URL in browser to test
// After 5 minutes, it should show "Access Denied"
```

### Test 4: Check Files in R2

```php
// In Tinker: php artisan tinker

// List all files
$allFiles = Storage::disk('r2')->allFiles();
print_r($allFiles);

// List files in user directory
$user = \App\Models\User::first();
$userFiles = Storage::disk('r2')->files("files/{$user->id}");
print_r($userFiles);
```

### Test 5: Delete File

```php
// In Tinker: php artisan tinker

// Find a test file
$file = \App\Models\File::where('name', 'test.png')->first();

// Delete it (moves to trash first)
$file->moveToTrash();

// Permanently delete
$file->deleteForever();

// Verify deleted from R2
Storage::disk('r2')->exists($file->storage_path); // false
```

---

## 9. Security Implementation

### Private Bucket
```php
// config/filesystems.php
'r2' => [
    'visibility' => 'private', // ✅ Bucket is private
],
```

### Signed Temporary URLs
```php
// 5-minute expiration
$url = Storage::disk('r2')->temporaryUrl(
    $file->storage_path,
    now()->addMinutes(5) // ✅ Expires in 5 minutes
);
```

### Access Control
```php
// Check user has access before generating URL
$hasAccess = $file->isOwnedBy(auth()->id()) || 
             FileShare::where('file_id', $file->id)
                      ->where('user_id', auth()->id())
                      ->exists();

if (!$hasAccess) {
    abort(403, 'Unauthorized action.'); // ✅ Authorization check
}
```

---

## 10. Common Operations

### Upload File to R2
```php
use Illuminate\Support\Facades\Storage;

// From uploaded file
$path = $request->file('document')->store('/files/user_123', 'r2');

// From string content
Storage::disk('r2')->put('path/to/file.txt', 'File contents');

// From existing file
Storage::disk('r2')->putFileAs('/files/user_123', $uploadedFile, 'custom-name.pdf');
```

### Download from R2
```php
// Stream download to browser
return Storage::disk('r2')->download('path/to/file.pdf', 'download-name.pdf');

// Get file contents
$contents = Storage::disk('r2')->get('path/to/file.txt');

// Check if exists
if (Storage::disk('r2')->exists('path/to/file.pdf')) {
    // File exists
}
```

### Generate Signed URL
```php
// 5 minutes
$url = Storage::disk('r2')->temporaryUrl('path/to/file.pdf', now()->addMinutes(5));

// 1 hour
$url = Storage::disk('r2')->temporaryUrl('path/to/file.pdf', now()->addHour());

// Custom expiration
$url = Storage::disk('r2')->temporaryUrl('path/to/file.pdf', now()->addMinutes(30));
```

### Delete from R2
```php
// Delete single file
Storage::disk('r2')->delete('path/to/file.pdf');

// Delete multiple files
Storage::disk('r2')->delete(['file1.pdf', 'file2.pdf']);

// Delete directory
Storage::disk('r2')->deleteDirectory('path/to/directory');
```

### List Files in R2
```php
// All files in directory
$files = Storage::disk('r2')->files('files/user_123');

// All files recursively
$files = Storage::disk('r2')->allFiles('files');

// All directories
$directories = Storage::disk('r2')->directories('files');
```

---

## Summary

✅ **Upload:** Uses `Storage::disk('r2')->put()`  
✅ **Preview:** Uses `Storage::disk('r2')->temporaryUrl()`  
✅ **Download:** Uses `Storage::disk('r2')->download()`  
✅ **Delete:** Uses `Storage::disk('r2')->delete()`  
✅ **Security:** Private bucket + 5-minute signed URLs  
✅ **Compatible:** Works with existing UI/UX  

---

**Status:** ✅ Production Ready  
**Bucket:** hamzavaultx (EU)  
**Endpoint:** https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
