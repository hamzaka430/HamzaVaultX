# 🚨 HEROKU 500 ERROR - IMMEDIATE ACTION PLAN

## ⚡ FASTEST FIX (5 Minutes)

### Option 1: Run the automated script
```powershell
.\fix-heroku-500.bat
```

### Option 2: Manual commands
```powershell
# Critical fixes (run these NOW)
heroku config:set LOG_CHANNEL=errorlog --app hamzavaultx
heroku config:set APP_DEBUG=true --app hamzavaultx
heroku config:set FILESYSTEM_DISK=local --app hamzavaultx
heroku run "php artisan optimize:clear" --app hamzavaultx
heroku restart --app hamzavaultx

# Watch logs and test app
heroku logs --tail --app hamzavaultx
# Then visit: https://hamzavaultx.herokuapp.com/
```

---

## 🎯 ROOT CAUSE (Why 500 with No Logs)

**The Problem:**
```
Laravel boots → LOG_CHANNEL=stack → writes to storage/logs/laravel.log
→ Heroku ephemeral FS → write fails → crash before logging → HTTP 500
```

**The Fix:**
```
LOG_CHANNEL=errorlog → writes to stderr → Heroku logs capture it → errors visible!
```

---

## 📋 EXPECTED RESULTS

### After running fix commands:

✅ **In browser:** You should see either:
- Your working app, OR
- Laravel debug page with ACTUAL error (because APP_DEBUG=true)

✅ **In heroku logs:** You should see:
```
[timestamp] app[web.1]: [error] Actual error message here
```

### If you see "Vite manifest not found":
```powershell
# Build assets and deploy
npm install
npm run build
git add public/build
git commit -m "Add Vite build assets"
git push heroku main
```

### If you see "Storage path not writable":
- Already fixed by `LOG_CHANNEL=errorlog`
- Confirm: `heroku config:get LOG_CHANNEL` should return `errorlog`

### If you see "Class 'PDO' not found" or PostgreSQL errors:
```powershell
# Check DATABASE_URL is set
heroku config:get DATABASE_URL --app hamzavaultx

# Test DB connection
heroku run "php artisan tinker --execute='DB::connection()->getPdo(); echo \"DB OK\";'" --app hamzavaultx
```

---

## 🔍 DIAGNOSTIC COMMANDS

```powershell
# 1. View all config vars
heroku config --app hamzavaultx

# 2. Test app bootstrap
heroku run "php artisan about" --app hamzavaultx

# 3. Check build assets
heroku run "ls -la public/build/" --app hamzavaultx

# 4. Check storage
heroku run "ls -la storage/logs/" --app hamzavaultx

# 5. Test database
heroku run "php artisan migrate:status" --app hamzavaultx

# 6. Test filesystem
heroku run "php -r 'echo is_writable(\"storage/logs\") ? \"Writable\" : \"Not writable\";'" --app hamzavaultx
```

---

## ✅ FINAL PRODUCTION CONFIG

After app works, set these (turn off debug):

```powershell
heroku config:set \
  APP_ENV=production \
  APP_DEBUG=false \
  LOG_CHANNEL=errorlog \
  LOG_LEVEL=info \
  FILESYSTEM_DISK=local \
  --app hamzavaultx
```

---

## 🔄 RE-ENABLE R2 STORAGE

Once app is stable, add R2 back:

```powershell
heroku config:set \
  AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1 \
  AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345 \
  AWS_DEFAULT_REGION=auto \
  AWS_BUCKET=hamzavaultx \
  AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com \
  AWS_USE_PATH_STYLE_ENDPOINT=false \
  FILESYSTEM_DISK=r2 \
  --app hamzavaultx

heroku restart --app hamzavaultx
```

---

## 📞 STILL NOT WORKING?

1. **Check buildpack order:**
   ```powershell
   heroku buildpacks --app hamzavaultx
   # Should show: heroku/nodejs THEN heroku/php
   ```

2. **Rebuild from scratch:**
   ```powershell
   heroku repo:purge_cache --app hamzavaultx
   git commit --allow-empty -m "Rebuild"
   git push heroku main --force
   ```

3. **Nuclear option (complete reset):**
   ```powershell
   heroku run "php artisan migrate:fresh --force" --app hamzavaultx
   heroku restart --app hamzavaultx
   ```

---

## 📚 REFERENCE DOCS

- Full guide: `HEROKU_PRODUCTION_FIX.md`
- Laravel docs: https://laravel.com/docs/11.x/deployment
- Heroku PHP: https://devcenter.heroku.com/articles/php-support

---

**Timeline:**
- 0-2 min: Run fix commands
- 2-5 min: See actual error or working app
- 5-10 min: Fix specific error (usually Vite assets)
- 10+ min: App fully working

Good luck! 🚀
