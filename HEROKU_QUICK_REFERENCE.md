# 🚀 Heroku Deployment - Quick Reference Card

## One-Time Setup Commands

```bash
# 1. Login to Heroku
heroku login

# 2. Create app
heroku create hamzavaultx-app

# 3. Set buildpacks (ORDER MATTERS!)
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php

# 4. Add PostgreSQL
heroku addons:create heroku-postgresql:essential-0

# 5. Generate APP_KEY locally
php artisan key:generate --show

# 6. Set essential config vars
heroku config:set APP_NAME="HamzaVaultX"
heroku config:set APP_ENV=production
heroku config:set APP_KEY="base64:YOUR_KEY_FROM_STEP_5"
heroku config:set APP_DEBUG=false
heroku config:set LOG_CHANNEL=stack
heroku config:set LOG_LEVEL=error

# 7. Set database config
heroku config:set DB_CONNECTION=pgsql

# 8. Set R2 config
heroku config:set FILESYSTEM_DISK=r2
heroku config:set AWS_ACCESS_KEY_ID=060d7d0b1a6a5e6df55bfa82ce9408f1
heroku config:set AWS_SECRET_ACCESS_KEY=5769400adef88e713bfd4e68fca9689091773c3150bb32be8c36da82cfbb1345
heroku config:set AWS_DEFAULT_REGION=auto
heroku config:set AWS_BUCKET=hamzavaultx
heroku config:set AWS_ENDPOINT=https://bd5dbfb9b2636cd9cec381f0f39bfd6c.r2.cloudflarestorage.com
heroku config:set AWS_USE_PATH_STYLE_ENDPOINT=false

# 9. Set session/cache drivers
heroku config:set SESSION_DRIVER=database
heroku config:set CACHE_DRIVER=database
heroku config:set QUEUE_CONNECTION=database

# 10. Deploy
git push heroku main

# 11. Run migrations
heroku run php artisan migrate --force

# 12. Set APP_URL (use your actual Heroku URL)
heroku config:set APP_URL=https://hamzavaultx-app.herokuapp.com

# 13. Open app
heroku open
```

---

## Daily Deployment Commands

```bash
# Commit changes
git add .
git commit -m "Your commit message"

# Deploy to Heroku
git push heroku main

# View logs
heroku logs --tail
```

---

## Troubleshooting Commands

### Check Status
```bash
heroku ps              # Dyno status
heroku info            # App info
heroku config          # All config vars
heroku addons          # Installed addons
heroku pg:info         # Database info
```

### Clear Caches
```bash
heroku run php artisan config:clear
heroku run php artisan cache:clear
heroku run php artisan route:clear
heroku run php artisan view:clear
```

### Re-cache
```bash
heroku run php artisan config:cache
heroku run php artisan route:cache
heroku run php artisan view:cache
```

### View Logs
```bash
heroku logs --tail                    # Real-time
heroku logs -n 500                    # Last 500 lines
heroku logs --tail | grep -i error    # Errors only
```

### Database
```bash
heroku run php artisan migrate --force    # Run migrations
heroku run php artisan db:seed --force    # Run seeders
heroku run php artisan tinker             # Open tinker
heroku pg:psql                            # PostgreSQL console
```

### Restart App
```bash
heroku restart
```

### Rollback (if deployment fails)
```bash
heroku releases          # View releases
heroku rollback          # Rollback to previous
heroku rollback v42      # Rollback to specific version
```

---

## Common Errors & Quick Fixes

### 500 Error
```bash
heroku logs --tail
heroku config:set APP_DEBUG=true  # Temporarily enable debug
heroku logs --tail                # Check for errors
heroku config:set APP_DEBUG=false # Disable debug
```

### Missing APP_KEY
```bash
php artisan key:generate --show
heroku config:set APP_KEY="base64:YOUR_KEY"
heroku restart
```

### Vite Manifest Missing
```bash
heroku buildpacks:clear
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php
git commit --allow-empty -m "Rebuild"
git push heroku main
```

### PostgreSQL Connection Error
```bash
heroku config:get DATABASE_URL    # Should exist
heroku config:set DB_CONNECTION=pgsql
heroku restart
```

### R2 Upload Issues
```bash
heroku run php artisan tinker
> Storage::disk('r2')->put('test.txt', 'test');
> Storage::disk('r2')->exists('test.txt');
> exit
```

### Config Not Updating
```bash
heroku run php artisan config:clear
heroku restart
```

---

## Emergency Contacts

- Heroku Status: https://status.heroku.com/
- Heroku Support: https://help.heroku.com/
- Application Logs: `heroku logs --tail`

---

## File Locations

- **Procfile:** `Procfile` (web server config)
- **App Config:** `app.json` (Heroku app definition)
- **PHP Settings:** `.user.ini` (PHP runtime config)
- **Buildpacks:** Set via CLI (nodejs, php)
- **Deployment Guide:** `HEROKU_DEPLOYMENT.md`

---

## Required Config Vars Checklist

- [ ] APP_NAME
- [ ] APP_ENV=production
- [ ] APP_KEY
- [ ] APP_DEBUG=false
- [ ] APP_URL
- [ ] LOG_CHANNEL=stack
- [ ] DB_CONNECTION=pgsql
- [ ] FILESYSTEM_DISK=r2
- [ ] AWS_ACCESS_KEY_ID
- [ ] AWS_SECRET_ACCESS_KEY
- [ ] AWS_DEFAULT_REGION=auto
- [ ] AWS_BUCKET
- [ ] AWS_ENDPOINT
- [ ] SESSION_DRIVER=database
- [ ] CACHE_DRIVER=database

---

## Buildpack Order (CRITICAL!)

1. heroku/nodejs (FIRST - builds Vite assets)
2. heroku/php (SECOND - runs composer)

Verify with: `heroku buildpacks`

---

## Post-Deployment Test

```bash
# 1. Open app
heroku open

# 2. Check logs
heroku logs --tail

# 3. Test R2
heroku run php artisan tinker
> Storage::disk('r2')->put('test.txt', 'test');
> exit

# 4. Test database
heroku run php artisan migrate:status

# 5. Monitor
heroku logs --tail
```

---

**Quick Help:** See `HEROKU_DEPLOYMENT.md` for detailed instructions
