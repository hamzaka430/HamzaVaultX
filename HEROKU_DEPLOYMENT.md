# 🚀 Heroku Production Deployment Guide

Complete guide to deploy HamzaVaultX (Google Drive Clone) to Heroku.

**Project Stack:**
- Laravel 11
- InertiaJS + Vue 3
- PostgreSQL (Database)
- Cloudflare R2 (File Storage)
- Vite (Frontend Build)

---

## ✅ PRE-DEPLOYMENT CHECKLIST

Before deploying, ensure you have:

- [x] Heroku account (paid plan for production apps)
- [x] Heroku CLI installed
- [x] Git repository initialized
- [x] Cloudflare R2 bucket created (hamzavaultx)
- [x] R2 credentials ready (Access Key ID, Secret Key, Endpoint)
- [x] All code committed to Git

---

## 📋 STEP 1 - PROCFILE CONFIGURATION

**File:** `Procfile` (already created in project root)

```
web: vendor/bin/heroku-php-apache2 public/
```

✅ **Status:** Already configured correctly.

**What it does:**
- Tells Heroku to use Apache2 web server
- Points to `public/` directory as document root
- Uses Heroku's optimized PHP buildpack

**Alternative (nginx):**
```
web: vendor/bin/heroku-php-nginx public/
```

---

## 🏗️ STEP 2 - BUILD CONFIGURATION

### Buildpack Order (CRITICAL!)

Heroku must build Node.js assets BEFORE PHP to ensure Vite manifest is available.

**Correct Order:**
1. **Node.js** (first) - Builds Vue/Vite assets
2. **PHP** (second) - Runs composer install

### Verification:

**File:** `app.json` (already created)

```json
"buildpacks": [
  {
    "url": "heroku/nodejs"
  },
  {
    "url": "heroku/php"
  }
]
```

✅ **Status:** Configured correctly.

### Vite Build Configuration

**File:** `package.json`

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "prod": "vite build --mode production"
}
```

✅ **Status:** Configured with production optimization.

**File:** `vite.config.js`

```javascript
export default defineConfig({
  plugins: [
    viteCompression({
      algorithm: "brotliCompress",
      ext: ".br",
      threshold: 10240,
    }),
    viteCompression({
      algorithm: "gzip",
      ext: ".gz",
    }),
    laravel({
      input: "resources/js/app.js",
      refresh: true,
    }),
    vue({ /* ... */ }),
  ],
  build: {
    manifest: true,
    outDir: "public/build",
    rollupOptions: {
      output: {
        manualChunks: undefined,
      },
    },
  },
});
```

✅ **Features:**
- Brotli compression
- Gzip compression
- Manifest generation
- Optimized chunk splitting

---

## 📦 STEP 3 - COMPOSER CONFIGURATION

**File:** `composer.json`

### Required Extensions:

```json
"require": {
  "php": "^8.2",
  "ext-pdo": "*",
  "ext-pgsql": "*",
  "league/flysystem-aws-s3-v3": "^3.0"
}
```

✅ **ext-pdo:** Required for database
✅ **ext-pgsql:** Required for PostgreSQL
✅ **league/flysystem-aws-s3-v3:** Required for R2

### Autoloader Optimization:

```json
"config": {
  "optimize-autoloader": true,
  "preferred-install": "dist",
  "sort-packages": true
}
```

✅ **optimize-autoloader:** Enabled for production performance

### Post-Deploy Scripts:

```json
"scripts": {
  "compile": [
    "npm install",
    "npm run build",
    "@php artisan config:cache",
    "@php artisan route:cache",
    "@php artisan view:cache"
  ]
}
```

✅ **compile:** Runs automatically on Heroku after deployment

**What it does:**
1. Installs Node dependencies
2. Builds Vite assets (creates manifest)
3. Caches Laravel config
4. Caches Laravel routes
5. Caches Blade views

---

## 🗄️ STEP 4 - DATABASE CONFIGURATION

**File:** `config/database.php`

### PostgreSQL Connection:

```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => env('DB_SSLMODE', 'require'),
],
```

✅ **DATABASE_URL:** Heroku automatically provides this
✅ **sslmode:** Set to 'require' for Heroku PostgreSQL

### Default Connection:

```php
'default' => env('DB_CONNECTION', 'mysql'),
```

⚠️ **IMPORTANT:** Set `DB_CONNECTION=pgsql` in Heroku config vars!

---

## 🔧 STEP 5 - HEROKU CONFIG VARS (Environment Variables)

### Required Config Vars:

Set these using Heroku CLI or Dashboard:

```bash
# Application
heroku config:set APP_NAME="HamzaVaultX"
heroku config:set APP_ENV=production
heroku config:set APP_KEY=base64:YOUR_GENERATED_KEY_HERE
heroku config:set APP_DEBUG=false
heroku config:set APP_URL=https://your-app-name.herokuapp.com
heroku config:set LOG_CHANNEL=stack
heroku config:set LOG_LEVEL=error

# Database (automatically set by Heroku PostgreSQL addon)
heroku config:set DB_CONNECTION=pgsql
# DATABASE_URL is auto-set by Heroku

# Cloudflare R2 Storage
heroku config:set FILESYSTEM_DISK=r2
heroku config:set AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1
heroku config:set AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345
heroku config:set AWS_DEFAULT_REGION=auto
heroku config:set AWS_BUCKET=hamzavaultx
heroku config:set AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
heroku config:set AWS_USE_PATH_STYLE_ENDPOINT=false

# Session & Cache (use database for stateless containers)
heroku config:set SESSION_DRIVER=database
heroku config:set CACHE_DRIVER=database
heroku config:set QUEUE_CONNECTION=database

# Mail (Optional - configure if using email features)
heroku config:set MAIL_MAILER=smtp
heroku config:set MAIL_HOST=smtp.mailtrap.io
heroku config:set MAIL_PORT=2525
heroku config:set MAIL_USERNAME=null
heroku config:set MAIL_PASSWORD=null
heroku config:set MAIL_ENCRYPTION=null
heroku config:set MAIL_FROM_ADDRESS="noreply@hamzavaultx.com"
heroku config:set MAIL_FROM_NAME="${APP_NAME}"
```

### Generate APP_KEY:

```bash
# On your local machine
php artisan key:generate --show

# Copy the output and set on Heroku
heroku config:set APP_KEY=base64:YOUR_KEY_HERE
```

---

## ⚡ STEP 6 - CACHE OPTIMIZATION

Heroku automatically runs these via `composer.json` compile script:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**To manually run after deployment:**

```bash
heroku run php artisan config:cache
heroku run php artisan route:cache
heroku run php artisan view:cache
```

**To clear caches:**

```bash
heroku run php artisan config:clear
heroku run php artisan route:clear
heroku run php artisan view:clear
heroku run php artisan cache:clear
```

---

## 💾 STEP 7 - STORAGE RULES

### ✅ Current Configuration:

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
    'options' => [
        'http' => [
            'verify' => base_path('cacert.pem'),
        ],
    ],
],
```

### Storage Rules:

✅ **DO NOT use local storage** - Heroku's filesystem is ephemeral
✅ **R2 disk is configured** - All uploads go to Cloudflare R2
✅ **Signed temporary URLs** - Used for previews (5-minute expiry)
✅ **Private bucket** - Files not publicly accessible

### File Operations:

**Upload:**
```php
Storage::disk('r2')->put('/files/user_id/file.pdf', $content);
```

**Preview (Signed URL):**
```php
$url = Storage::disk('r2')->temporaryUrl($path, now()->addMinutes(5));
```

**Download:**
```php
return Storage::disk('r2')->download($path, $filename);
```

**Delete:**
```php
Storage::disk('r2')->delete($path);
```

---

## 🚀 STEP 8 - DEPLOYMENT COMMANDS

### Complete Deployment Process:

#### 1. Login to Heroku:

```bash
heroku login
```

#### 2. Create Heroku App:

```bash
# Create with auto-generated name
heroku create

# OR create with custom name
heroku create hamzavaultx-app
```

**Output:**
```
Creating ⬢ hamzavaultx-app... done
https://hamzavaultx-app.herokuapp.com/ | https://git.heroku.com/hamzavaultx-app.git
```

#### 3. Set Buildpacks (in correct order):

```bash
# Add Node.js buildpack (FIRST)
heroku buildpacks:add heroku/nodejs

# Add PHP buildpack (SECOND)
heroku buildpacks:add heroku/php
```

**Verify order:**
```bash
heroku buildpacks
```

**Expected output:**
```
=== hamzavaultx-app Buildpack URLs
1. heroku/nodejs
2. heroku/php
```

#### 4. Add PostgreSQL Database:

```bash
# Essential plan (paid, $5/month)
heroku addons:create heroku-postgresql:essential-0

# OR Basic plan (paid, $9/month)
heroku addons:create heroku-postgresql:basic

# OR Mini plan (paid, $5/month)
heroku addons:create heroku-postgresql:mini
```

**Verify:**
```bash
heroku addons
heroku pg:info
```

#### 5. Set Environment Variables:

```bash
# Application Config
heroku config:set APP_NAME="HamzaVaultX"
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set LOG_CHANNEL=stack
heroku config:set LOG_LEVEL=error

# Generate and set APP_KEY
php artisan key:generate --show
# Copy the output, then:
heroku config:set APP_KEY="base64:YOUR_GENERATED_KEY_HERE"

# Database
heroku config:set DB_CONNECTION=pgsql

# Cloudflare R2
heroku config:set FILESYSTEM_DISK=r2
heroku config:set AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1
heroku config:set AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345
heroku config:set AWS_DEFAULT_REGION=auto
heroku config:set AWS_BUCKET=hamzavaultx
heroku config:set AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
heroku config:set AWS_USE_PATH_STYLE_ENDPOINT=false

# Session & Cache
heroku config:set SESSION_DRIVER=database
heroku config:set CACHE_DRIVER=database
heroku config:set QUEUE_CONNECTION=database
```

**Verify all config vars:**
```bash
heroku config
```

#### 6. Commit and Push to Heroku:

```bash
# Ensure all changes are committed
git add .
git commit -m "Prepare for Heroku deployment"

# Push to Heroku (triggers build)
git push heroku main

# OR if your default branch is master:
git push heroku master
```

**Build Process:**
1. Detects buildpacks
2. Node.js: Installs npm packages, runs `npm run build`
3. PHP: Installs composer packages, runs `composer compile`
4. Creates slug
5. Releases application

#### 7. Run Database Migrations:

```bash
heroku run php artisan migrate --force
```

**IMPORTANT:** `--force` is required in production!

#### 8. Set APP_URL After Deployment:

```bash
# Get your app URL
heroku info

# Set APP_URL
heroku config:set APP_URL=https://hamzavaultx-app.herokuapp.com
```

#### 9. Open Application:

```bash
heroku open
```

---

## 🐛 STEP 9 - TROUBLESHOOTING

### 1. 500 Internal Server Error

**Symptoms:**
- Application shows generic error page
- "Server Error" message

**Diagnosis:**
```bash
# Check application logs
heroku logs --tail

# Check for errors
heroku logs --tail | grep -i error
```

**Common Causes & Fixes:**

#### A. Missing APP_KEY
```bash
# Generate locally
php artisan key:generate --show

# Set on Heroku
heroku config:set APP_KEY="base64:YOUR_KEY_HERE"

# Restart app
heroku restart
```

#### B. Config Cache Issues
```bash
# Clear all caches
heroku run php artisan config:clear
heroku run php artisan cache:clear
heroku run php artisan route:clear
heroku run php artisan view:clear

# Re-cache
heroku run php artisan config:cache
heroku run php artisan route:cache
heroku run php artisan view:cache
```

#### C. APP_DEBUG Set to True
```bash
# Disable debug in production
heroku config:set APP_DEBUG=false

# View detailed error temporarily
heroku config:set APP_DEBUG=true
heroku logs --tail
# Then set back to false
heroku config:set APP_DEBUG=false
```

---

### 2. Vite Manifest Missing Error

**Error Message:**
```
Vite manifest not found at: /app/public/build/manifest.json
```

**Cause:** Frontend assets not built during deployment

**Fix:**

#### A. Verify Buildpack Order:
```bash
heroku buildpacks

# Should show:
# 1. heroku/nodejs
# 2. heroku/php
```

If wrong order:
```bash
heroku buildpacks:clear
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php
git commit --allow-empty -m "Rebuild with correct buildpacks"
git push heroku main
```

#### B. Verify package.json Build Script:
```json
"scripts": {
  "build": "vite build"
}
```

#### C. Verify composer.json compile Script:
```json
"scripts": {
  "compile": [
    "npm install",
    "npm run build",
    "@php artisan config:cache",
    "@php artisan route:cache",
    "@php artisan view:cache"
  ]
}
```

#### D. Manual Build:
```bash
# SSH into Heroku dyno
heroku run bash

# Inside dyno:
npm install
npm run build
ls -la public/build/

# Should see manifest.json
exit

# Restart app
heroku restart
```

#### E. Check Build Logs:
```bash
# View recent deployment logs
heroku releases
heroku releases:output

# Look for npm build errors
```

---

### 3. PostgreSQL Connection Error

**Error Message:**
```
SQLSTATE[08006] [7] could not connect to server
Connection refused
```

**Fix:**

#### A. Verify PostgreSQL Addon:
```bash
heroku addons
heroku pg:info
```

Should show active PostgreSQL database.

#### B. Check DATABASE_URL:
```bash
heroku config:get DATABASE_URL

# Should output: postgres://user:pass@host:5432/dbname
```

#### C. Verify DB_CONNECTION:
```bash
heroku config:get DB_CONNECTION

# Should be: pgsql
```

If not set:
```bash
heroku config:set DB_CONNECTION=pgsql
```

#### D. SSL Mode:
Ensure `config/database.php` has:
```php
'sslmode' => env('DB_SSLMODE', 'require'),
```

#### E. Test Connection:
```bash
heroku run php artisan tinker

# Inside tinker:
DB::connection()->getPdo();
# Should connect without error

# Test query:
DB::select('SELECT 1 as test');

exit
```

#### F. Run Migrations:
```bash
heroku run php artisan migrate --force
```

---

### 4. R2 Upload Not Working

**Symptoms:**
- File uploads fail silently
- "Unable to upload file" error
- Files don't appear in R2 bucket

**Fix:**

#### A. Verify R2 Config Vars:
```bash
heroku config:get FILESYSTEM_DISK
# Should be: r2

heroku config:get AWS_ACCESS_KEY_ID
heroku config:get AWS_SECRET_ACCESS_KEY
heroku config:get AWS_BUCKET
heroku config:get AWS_ENDPOINT
heroku config:get AWS_DEFAULT_REGION
```

#### B. Test R2 Connection:
```bash
heroku run php artisan tinker

# Inside tinker:
use Illuminate\Support\Facades\Storage;

# Test upload
Storage::disk('r2')->put('test.txt', 'Hello from Heroku!');

# Test exists
Storage::disk('r2')->exists('test.txt');
// Should return: true

# Generate signed URL
Storage::disk('r2')->temporaryUrl('test.txt', now()->addMinutes(5));
// Should return: https://hamzavaultx.bd5dbfb9...

# Delete test file
Storage::disk('r2')->delete('test.txt');

exit
```

#### C. Check SSL Certificate:
On Heroku, SSL certificates are handled differently.

Update `config/filesystems.php`:
```php
'r2' => [
    // ...
    'options' => [
        'http' => [
            'verify' => env('AWS_SSL_VERIFY', true),
        ],
    ],
],
```

Then:
```bash
# If SSL errors persist:
heroku config:set AWS_SSL_VERIFY=false

# Not recommended for production, but can help diagnose
```

#### D. Check Application Logs:
```bash
heroku logs --tail | grep -i "storage\|s3\|r2"
```

#### E. Verify Credentials in Cloudflare:
- Login to Cloudflare Dashboard
- Navigate to R2
- Verify bucket name: `hamzavaultx`
- Verify access key ID and secret key are correct
- Ensure API token has "Admin Read & Write" permissions

---

### 5. Config Not Updating

**Symptoms:**
- Changed environment variables but app still uses old values
- Config changes don't take effect

**Fix:**

#### A. Clear Config Cache:
```bash
heroku run php artisan config:clear
heroku run php artisan cache:clear
```

#### B. Restart Application:
```bash
heroku restart
```

#### C. Verify Config Vars:
```bash
heroku config

# Check specific var
heroku config:get VAR_NAME
```

#### D. Update Config Var:
```bash
heroku config:set VAR_NAME=new_value
```

#### E. Re-cache Config:
```bash
heroku run php artisan config:cache
heroku restart
```

---

### 6. Session/Login Issues

**Symptoms:**
- Can't login
- Logged out immediately after login
- CSRF token mismatch

**Fix:**

#### A. Use Database Session Driver:
```bash
heroku config:set SESSION_DRIVER=database
```

#### B. Run Session Table Migration:
```bash
# Check if migration exists
heroku run php artisan migrate:status

# If not, create locally then push:
php artisan session:table
git add .
git commit -m "Add session table migration"
git push heroku main

# Run migration on Heroku
heroku run php artisan migrate --force
```

#### C. Verify APP_URL:
```bash
heroku config:get APP_URL

# Should match your actual Heroku URL
heroku config:set APP_URL=https://your-app.herokuapp.com
```

#### D. Check Cookie Settings:
Ensure `config/session.php` has:
```php
'secure' => env('SESSION_SECURE_COOKIE', true),
'same_site' => 'lax',
```

---

### 7. Memory Limit Issues

**Error:**
```
Allowed memory size exhausted
```

**Fix:**

#### A. Increase PHP Memory Limit:
Create `.user.ini` in project root:
```ini
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
```

#### B. Upgrade Dyno:
```bash
# List current dyno type
heroku ps

# Upgrade to Standard-1X (512MB RAM)
heroku ps:resize web=standard-1x

# OR Standard-2X (1GB RAM)
heroku ps:resize web=standard-2x
```

---

### 8. Build Fails

**Symptoms:**
- `git push heroku main` fails
- Buildpack errors

**Common Causes & Fixes:**

#### A. Node.js Version:
Specify in `package.json`:
```json
"engines": {
  "node": "18.x",
  "npm": "9.x"
}
```

#### B. PHP Version:
Specify in `composer.json`:
```json
"require": {
  "php": "^8.2"
}
```

#### C. Buildpack Order:
```bash
heroku buildpacks:clear
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php
```

#### D. View Build Logs:
```bash
heroku logs --tail

# Or during push:
git push heroku main --verbose
```

#### E. Clear Build Cache:
```bash
heroku plugins:install heroku-builds
heroku builds:cache:purge
git push heroku main
```

---

## 📊 MONITORING & MAINTENANCE

### View Logs:
```bash
# Real-time logs
heroku logs --tail

# Last 1000 lines
heroku logs -n 1000

# Filter by source
heroku logs --source app
heroku logs --source heroku

# Search logs
heroku logs --tail | grep -i error
```

### Check Application Status:
```bash
# Dyno status
heroku ps

# App info
heroku info

# Database info
heroku pg:info

# Addon info
heroku addons
```

### Database Maintenance:
```bash
# Run migration
heroku run php artisan migrate --force

# Run seeder
heroku run php artisan db:seed --force

# Database backup
heroku pg:backups:capture
heroku pg:backups:download

# View database credentials
heroku pg:credentials:url
```

### Performance Monitoring:
```bash
# View metrics
heroku metrics

# View dyno usage
heroku ps -a your-app-name

# View response times
heroku logs --tail | grep -i "request\|response"
```

### Scaling:
```bash
# Scale web dynos
heroku ps:scale web=2

# Upgrade dyno type
heroku ps:resize web=standard-1x

# View pricing
heroku pricing
```

---

## 🔒 SECURITY CHECKLIST

Before going live:

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` generated
- [ ] HTTPS enforced (Heroku automatic)
- [ ] Database backups enabled
- [ ] R2 bucket is private
- [ ] Signed URLs for file access
- [ ] No credentials in code/git
- [ ] `.env` not committed
- [ ] Error logging enabled
- [ ] Monitoring set up

---

## 📝 POST-DEPLOYMENT CHECKLIST

After successful deployment:

- [ ] Application loads without errors
- [ ] Can register/login
- [ ] Can upload files to R2
- [ ] Can preview files (signed URLs work)
- [ ] Can download files
- [ ] Can delete files
- [ ] Sessions persist
- [ ] Database queries work
- [ ] All pages load correctly
- [ ] Vite assets load (CSS, JS)
- [ ] No console errors in browser

---

## 🎯 QUICK REFERENCE

### Essential Commands:

```bash
# Deploy
git push heroku main

# View logs
heroku logs --tail

# Run artisan command
heroku run php artisan COMMAND

# Open app
heroku open

# Restart app
heroku restart

# Access bash
heroku run bash

# Run migrations
heroku run php artisan migrate --force

# Clear caches
heroku run php artisan optimize:clear

# Config
heroku config
heroku config:set KEY=VALUE
heroku config:get KEY
```

### Emergency Rollback:

```bash
# List releases
heroku releases

# Rollback to previous release
heroku rollback

# Or specific version
heroku rollback v42
```

---

## 📞 SUPPORT RESOURCES

- **Heroku Docs:** https://devcenter.heroku.com/
- **Laravel Docs:** https://laravel.com/docs/11.x
- **Cloudflare R2 Docs:** https://developers.cloudflare.com/r2/
- **Heroku Status:** https://status.heroku.com/
- **Heroku Support:** https://help.heroku.com/

---

## ✅ DEPLOYMENT COMPLETE!

Your Laravel Google Drive Clone is now live on Heroku!

**Access your app:**
```bash
heroku open
```

**Monitor in real-time:**
```bash
heroku logs --tail
```

Congratulations! 🎉

---

**Deployed:** February 15, 2026
**Application:** HamzaVaultX
**Stack:** Laravel 11 + Vue 3 + PostgreSQL + Cloudflare R2
**Platform:** Heroku
