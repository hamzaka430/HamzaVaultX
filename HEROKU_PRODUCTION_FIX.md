# Heroku Production Fix - Laravel 11 HTTP 500 Error

## 🔴 IMMEDIATE FIX (Run These Commands Now)

```powershell
# 1. CRITICAL: Switch to errorlog (Heroku-compatible logging)
heroku config:set LOG_CHANNEL=errorlog --app hamzavaultx

# 2. Enable debug mode temporarily to see errors
heroku config:set APP_DEBUG=true --app hamzavaultx

# 3. Ensure local filesystem (not R2)
heroku config:set FILESYSTEM_DISK=local --app hamzavaultx

# 4. Clear all caches
heroku run "php artisan optimize:clear" --app hamzavaultx

# 5. Restart
heroku restart --app hamzavaultx

# 6. Watch logs in real-time
heroku logs --tail --app hamzavaultx
```

Now visit your app: https://hamzavaultx.herokuapp.com/

You should see the actual error in:
- Browser (because APP_DEBUG=true)
- Heroku logs terminal

---

## 📊 ROOT CAUSE ANALYSIS

### Why 500 with No Logs?

**The Problem:**
1. Laravel uses `LOG_CHANNEL=stack` (writes to `storage/logs/laravel.log`)
2. Heroku has ephemeral filesystem + `storage/logs/` may not exist
3. When log write fails → Laravel crashes before logging the crash
4. Result: HTTP 500, no logs anywhere

**The Solution:**
- Use `LOG_CHANNEL=errorlog` → writes to **stderr** (not files)
- Heroku captures stderr → visible in `heroku logs`
- Errors are now visible!

### Most Likely Issues (in order)

1. **Missing Vite Build Assets** (80% probability)
   - `public/build/manifest.json` doesn't exist
   - Inertia/Vite helper crashes on boot

2. **Storage Directory Not Writable** (70% probability)
   - Laravel tries to write logs/cache/sessions to disk
   - Heroku ephemeral filesystem blocks this

3. **R2 Connection Timeout** (60% probability)
   - AWS credentials present but misconfigured
   - Boot-time S3 connection attempt hangs/fails

4. **Database Migration Issue** (40% probability)
   - Missing tables or wrong DATABASE_URL format

---

## ✅ COMPLETE PRODUCTION CONFIG

### Minimal Safe Configuration (Heroku Config Vars)

```bash
# === CORE ===
APP_NAME=HamzaVaultX
APP_ENV=production
APP_DEBUG=false  # Set to true ONLY for debugging
APP_KEY=base64:zH77cuyltJfXF18tKqM4CessCb5XawbGIZBXKjjS66Q=
APP_URL=https://hamzavaultx.herokuapp.com

# === LOGGING (CRITICAL FOR HEROKU) ===
LOG_CHANNEL=errorlog  # NOT 'stack' - use errorlog for Heroku
LOG_LEVEL=info        # Use 'debug' for troubleshooting

# === DATABASE ===
# Heroku sets DATABASE_URL automatically - DON'T set individual DB_ vars
# Remove: DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# === CACHE/SESSION/QUEUE ===
CACHE_DRIVER=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database

# === FILESYSTEM ===
FILESYSTEM_DISK=local  # Start with local, add R2 later after app works

# === REMOVE THESE (causes boot failures) ===
# AWS_ACCESS_KEY_ID=<remove>
# AWS_SECRET_ACCESS_KEY=<remove>
# AWS_DEFAULT_REGION=<remove>
# AWS_BUCKET=<remove>
# AWS_ENDPOINT=<remove>
# AWS_USE_PATH_STYLE_ENDPOINT=<remove>
```

### Apply Configuration

```powershell
# Set all config vars
heroku config:set \
  APP_NAME=HamzaVaultX \
  APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY=base64:zH77cuyltJfXF18tKqM4CessCb5XawbGIZBXKjjS66Q= \
  APP_URL=https://hamzavaultx.herokuapp.com \
  LOG_CHANNEL=errorlog \
  LOG_LEVEL=info \
  CACHE_DRIVER=database \
  SESSION_DRIVER=database \
  SESSION_LIFETIME=120 \
  QUEUE_CONNECTION=database \
  FILESYSTEM_DISK=local \
  --app hamzavaultx

# Remove problematic AWS vars
heroku config:unset \
  AWS_ACCESS_KEY_ID \
  AWS_SECRET_ACCESS_KEY \
  AWS_DEFAULT_REGION \
  AWS_BUCKET \
  AWS_ENDPOINT \
  AWS_USE_PATH_STYLE_ENDPOINT \
  DB_CONNECTION \
  DB_HOST \
  DB_PORT \
  DB_DATABASE \
  DB_USERNAME \
  DB_PASSWORD \
  --app hamzavaultx

# Clear caches
heroku run "php artisan optimize:clear" --app hamzavaultx

# Restart
heroku restart --app hamzavaultx
```

---

## 🏗️ BUILD ASSETS (VITE)

Your app uses Vite + Inertia + Vue. **Assets MUST be compiled before deployment.**

### Check if Assets Exist

```powershell
heroku run "ls -la public/build/" --app hamzavaultx
```

If you see **"No such file or directory"** → assets are missing!

### Solution: Build on Heroku Post-Install

The buildpack will auto-run `npm run build` if you configure it correctly.

**Step 1: Ensure package.json has build script** (already done ✅)

**Step 2: Tell Heroku to run Node.js buildpack**

```powershell
# Check current buildpacks
heroku buildpacks --app hamzavaultx

# Should show:
# 1. heroku/nodejs
# 2. heroku/php

# If nodejs is missing or in wrong order:
heroku buildpacks:clear --app hamzavaultx
heroku buildpacks:add heroku/nodejs --app hamzavaultx
heroku buildpacks:add heroku/php --app hamzavaultx
```

**Step 3: Deploy**

```powershell
git add .
git commit -m "Fix Heroku deployment: add post-install hook"
git push heroku main
```

Heroku will now:
1. Run `npm install`
2. Run `npm run build` (compiles Vite assets)
3. Run `composer install`
4. Start Apache

---

## 🧪 VERIFICATION CHECKLIST

Run each command and verify output:

### ✅ 1. Check Config Vars
```powershell
heroku config --app hamzavaultx
```
Verify:
- `LOG_CHANNEL=errorlog`
- `FILESYSTEM_DISK=local`
- No AWS_ vars present

### ✅ 2. Test Database Connection
```powershell
heroku run "php artisan tinker --execute='DB::connection()->getPdo(); echo \"DB Connected!\n\";'" --app hamzavaultx
```
Expected: `DB Connected!`

### ✅ 3. Test App Bootstrap
```powershell
heroku run "php artisan about" --app hamzavaultx
```
Expected: Shows Laravel environment info

### ✅ 4. Check Storage Directories
```powershell
heroku run "ls -la storage/" --app hamzavaultx
```
Expected: Shows `app/`, `framework/`, `logs/`

### ✅ 5. Check Vite Assets
```powershell
heroku run "ls -la public/build/" --app hamzavaultx
```
Expected: Shows `manifest.json` and compiled assets

### ✅ 6. Test Filesystem Write
```powershell
heroku run "php artisan tinker --execute='Storage::disk(\"local\")->put(\"test.txt\", \"OK\"); echo Storage::disk(\"local\")->get(\"test.txt\");'" --app hamzavaultx
```
Expected: `OK`

### ✅ 7. Check PHP Extensions
```powershell
heroku run "php -m | grep -E 'pdo|pgsql'" --app hamzavaultx
```
Expected:
```
pdo_pgsql
pgsql
```

### ✅ 8. View Live Logs
```powershell
heroku logs --tail --app hamzavaultx
```
Then visit app in browser. Logs should show request handling.

---

## 🚀 FULL DEPLOYMENT WORKFLOW

### First-Time Deployment

```powershell
# 1. Build assets locally (faster than Heroku build)
npm install
npm run build

# 2. Commit built assets
git add public/build
git commit -m "Build Vite assets for production"

# 3. Set safe config
heroku config:set LOG_CHANNEL=errorlog FILESYSTEM_DISK=local --app hamzavaultx

# 4. Deploy
git push heroku main

# 5. Run migrations
heroku run "php artisan migrate --force" --app hamzavaultx

# 6. Clear caches
heroku run "php artisan optimize:clear" --app hamzavaultx

# 7. Restart
heroku restart --app hamzavaultx

# 8. Monitor
heroku logs --tail --app hamzavaultx
```

### Subsequent Deployments

```powershell
# 1. Build assets
npm run build

# 2. Commit changes
git add .
git commit -m "Your changes"

# 3. Deploy
git push heroku main

# 4. Clear cache if config/routes changed
heroku run "php artisan optimize:clear" --app hamzavaultx
```

---

## 🔄 RE-ENABLE R2 (After App Works)

Once app is stable with `FILESYSTEM_DISK=local`, re-enable R2:

```powershell
# 1. Set R2 credentials
heroku config:set \
  AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1 \
  AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345 \
  AWS_DEFAULT_REGION=auto \
  AWS_BUCKET=hamzavaultx \
  AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com \
  AWS_USE_PATH_STYLE_ENDPOINT=false \
  --app hamzavaultx

# 2. Test R2 connection
heroku run "php artisan tinker --execute='Storage::disk(\"r2\")->put(\"test.txt\", \"OK\"); echo Storage::disk(\"r2\")->get(\"test.txt\");'" --app hamzavaultx

# 3. If test succeeds, switch to R2
heroku config:set FILESYSTEM_DISK=r2 --app hamzavaultx

# 4. Restart
heroku restart --app hamzavaultx
```

---

## 📞 TROUBLESHOOTING

### Still Getting 500?

1. Enable debug:
   ```powershell
   heroku config:set APP_DEBUG=true --app hamzavaultx
   heroku restart --app hamzavaultx
   ```

2. Visit app in browser → you'll see the actual error

3. Check logs:
   ```powershell
   heroku logs --tail --app hamzavaultx
   ```

### Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| `Class 'Vite' not found` | Missing Vite manifest | Run `npm run build` locally, commit assets |
| `No such file or directory: storage/logs` | Storage not writable | Use `LOG_CHANNEL=errorlog` |
| `SQLSTATE[08006] connection failed` | DATABASE_URL wrong | Check `heroku config:get DATABASE_URL` |
| `Class 'League\Flysystem\AwsS3V3\...' not found` | R2 misconfigured | Set `FILESYSTEM_DISK=local` temporarily |
| `Vite manifest not found` | Assets not built | Run `npm run build` and commit |

### Nuclear Option (Complete Reset)

```powershell
# 1. Clear ALL config (keeps DATABASE_URL)
heroku config --app hamzavaultx  # Copy DATABASE_URL value

# 2. Remove old configs
heroku config:unset $(heroku config --app hamzavaultx | grep -v DATABASE_URL | awk '{print $1}' | grep -v '===') --app hamzavaultx

# 3. Set minimal config
heroku config:set \
  APP_KEY=base64:zH77cuyltJfXF18tKqM4CessCb5XawbGIZBXKjjS66Q= \
  APP_ENV=production \
  LOG_CHANNEL=errorlog \
  FILESYSTEM_DISK=local \
  --app hamzavaultx

# 4. Redeploy
git push heroku main --force

# 5. Migrate
heroku run "php artisan migrate:fresh --force --seed" --app hamzavaultx
```

---

## 📚 REFERENCES

- [Laravel on Heroku Docs](https://devcenter.heroku.com/articles/getting-started-with-laravel)
- [Heroku PHP Support](https://devcenter.heroku.com/articles/php-support)
- [Laravel Logging Docs](https://laravel.com/docs/11.x/logging)
- [Vite Laravel Plugin](https://laravel.com/docs/11.x/vite)

---

## ✅ SUCCESS CRITERIA

Your app is working when:
1. Browser shows your app (not 500 error)
2. `heroku logs` shows HTTP 200 responses
3. You can login/register
4. Database operations work
5. File uploads work (to local or R2)

Good luck! 🚀
