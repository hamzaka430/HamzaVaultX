# 🚀 Pre-Deployment Checklist - Heroku

Use this checklist before deploying to Heroku to ensure everything is configured correctly.

---

## ✅ LOCAL VERIFICATION

### 1. Files Exist
- [ ] `Procfile` exists in project root
- [ ] `app.json` exists in project root
- [ ] `.user.ini` exists in project root
- [ ] `composer.json` configured
- [ ] `package.json` configured
- [ ] `vite.config.js` configured

### 2. Git Repository
- [ ] Git initialized: `git status` works
- [ ] All changes committed
- [ ] No sensitive data in commits (check `.env` is in `.gitignore`)
- [ ] Branch is `main` or `master`

### 3. Dependencies
- [ ] `composer install` completes without errors
- [ ] `npm install` completes without errors
- [ ] `npm run build` completes without errors
- [ ] Vite manifest generated in `public/build/manifest.json`

### 4. Database Configuration
- [ ] `config/database.php` has PostgreSQL config
- [ ] `sslmode` set to `require` in pgsql connection
- [ ] Migrations ready: `php artisan migrate:status`

### 5. R2 Storage
- [ ] R2 bucket exists: `hamzavaultx`
- [ ] R2 credentials ready (Access Key ID, Secret Key)
- [ ] R2 endpoint URL ready
- [ ] Test R2 connection works locally

### 6. Application Key
- [ ] Generate APP_KEY: `php artisan key:generate --show`
- [ ] Copy generated key for Heroku config

---

## 🔧 HEROKU SETUP

### 7. Heroku Account
- [ ] Heroku account created
- [ ] Paid plan activated (required for production)
- [ ] Payment method added
- [ ] Heroku CLI installed: `heroku --version`
- [ ] Logged in: `heroku login`

### 8. Create Heroku App
- [ ] App created: `heroku create app-name`
- [ ] App name noted for reference
- [ ] App URL noted

### 9. Buildpacks
- [ ] Node.js buildpack added FIRST: `heroku buildpacks:add heroku/nodejs`
- [ ] PHP buildpack added SECOND: `heroku buildpacks:add heroku/php`
- [ ] Order verified: `heroku buildpacks`

### 10. PostgreSQL Addon
- [ ] PostgreSQL addon created: `heroku addons:create heroku-postgresql:essential-0`
- [ ] DATABASE_URL automatically set
- [ ] Database verified: `heroku pg:info`

---

## 🔐 CONFIG VARS

### 11. Application Config
- [ ] `APP_NAME` set
- [ ] `APP_ENV=production` set
- [ ] `APP_KEY` set (from step 6)
- [ ] `APP_DEBUG=false` set
- [ ] `LOG_CHANNEL=stack` set
- [ ] `LOG_LEVEL=error` set

### 12. Database Config
- [ ] `DB_CONNECTION=pgsql` set
- [ ] `DATABASE_URL` exists (auto-set by addon)

### 13. R2 Storage Config
- [ ] `FILESYSTEM_DISK=r2` set
- [ ] `AWS_ACCESS_KEY_ID` set
- [ ] `AWS_SECRET_ACCESS_KEY` set
- [ ] `AWS_DEFAULT_REGION=auto` set
- [ ] `AWS_BUCKET=hamzavaultx` set
- [ ] `AWS_ENDPOINT` set (full URL)
- [ ] `AWS_USE_PATH_STYLE_ENDPOINT=false` set

### 14. Session/Cache Config
- [ ] `SESSION_DRIVER=database` set
- [ ] `CACHE_DRIVER=database` set
- [ ] `QUEUE_CONNECTION=database` set

### 15. Verify All Config Vars
- [ ] Run: `heroku config`
- [ ] All required vars present
- [ ] No typos in values

---

## 📦 DEPLOYMENT

### 16. Initial Deployment
- [ ] All code committed: `git status` clean
- [ ] Push to Heroku: `git push heroku main`
- [ ] Build succeeds without errors
- [ ] No "Vite manifest missing" error
- [ ] Deploy completes successfully

### 17. Database Migration
- [ ] Run: `heroku run php artisan migrate --force`
- [ ] All migrations run successfully
- [ ] No migration errors

### 18. Set APP_URL
- [ ] Get app URL: `heroku info`
- [ ] Set: `heroku config:set APP_URL=https://your-app.herokuapp.com`

### 19. Cache Optimization
- [ ] Config cached: `heroku run php artisan config:cache`
- [ ] Routes cached: `heroku run php artisan route:cache`
- [ ] Views cached: `heroku run php artisan view:cache`
- [ ] Or verify compile script ran automatically

---

## 🧪 POST-DEPLOYMENT TESTING

### 20. Application Access
- [ ] Open app: `heroku open`
- [ ] Homepage loads without errors
- [ ] No 500 errors
- [ ] CSS/JS assets load correctly
- [ ] No console errors in browser

### 21. User Authentication
- [ ] Can access registration page
- [ ] Can register new account
- [ ] Email validation works (if enabled)
- [ ] Can login
- [ ] Session persists
- [ ] Can logout

### 22. File Operations
- [ ] Can navigate to "My Files"
- [ ] Can create folder
- [ ] Can upload file (test with small file first)
- [ ] File appears in file list
- [ ] Can preview file (signed URL works)
- [ ] Preview URL expires after 5 minutes
- [ ] Can download file
- [ ] Downloaded file is correct
- [ ] Can delete file
- [ ] File removed from R2

### 23. R2 Storage Verification
- [ ] Login to Cloudflare dashboard
- [ ] Navigate to R2 bucket: hamzavaultx
- [ ] Uploaded files visible in bucket
- [ ] Files under `/files/` directory
- [ ] Deleted files removed from bucket

### 24. Database Verification
- [ ] Run: `heroku run php artisan tinker`
- [ ] Test query: `DB::select('SELECT 1 as test');`
- [ ] Query succeeds
- [ ] Check users: `\App\Models\User::count();`
- [ ] Count matches expected

### 25. Logging
- [ ] View logs: `heroku logs --tail`
- [ ] No critical errors
- [ ] Application logs working
- [ ] Database queries logged (if enabled)

---

## 📊 MONITORING

### 26. Dyno Status
- [ ] Check: `heroku ps`
- [ ] Web dyno is running
- [ ] No crashed dynos
- [ ] Dyno type is correct (basic, standard-1x, etc.)

### 27. Database Status
- [ ] Check: `heroku pg:info`
- [ ] Database is available
- [ ] Current connections shown
- [ ] No connection limit warnings

### 28. Resource Limits
- [ ] Check dyno hours remaining (if on free plan)
- [ ] Check database row limit (if on free plan)
- [ ] Check R2 storage usage in Cloudflare
- [ ] Set up billing alerts (recommended)

---

## 🔒 SECURITY VERIFICATION

### 29. Environment Settings
- [ ] `APP_ENV=production` confirmed
- [ ] `APP_DEBUG=false` confirmed
- [ ] `APP_KEY` is strong (base64 encoded)
- [ ] No credentials in code
- [ ] No sensitive data in logs

### 30. SSL/HTTPS
- [ ] Application URL uses HTTPS
- [ ] No mixed content warnings
- [ ] SSL certificate valid (Heroku automatic)
- [ ] R2 connections use HTTPS

### 31. R2 Bucket Security
- [ ] Bucket visibility is PRIVATE
- [ ] No public access to files
- [ ] Files only accessible via signed URLs
- [ ] Signed URLs expire (5 minutes)

---

## 📝 DOCUMENTATION

### 32. Reference Documents
- [ ] Read: `HEROKU_DEPLOYMENT.md`
- [ ] Read: `HEROKU_QUICK_REFERENCE.md`
- [ ] Bookmark for future reference
- [ ] Share with team members

### 33. Troubleshooting Ready
- [ ] Know how to check logs: `heroku logs --tail`
- [ ] Know how to restart app: `heroku restart`
- [ ] Know how to rollback: `heroku rollback`
- [ ] Know how to access tinker: `heroku run php artisan tinker`

---

## 🎯 OPTIONAL BUT RECOMMENDED

### 34. Domain Setup (Optional)
- [ ] Custom domain purchased
- [ ] Domain added to Heroku: `heroku domains:add`
- [ ] DNS configured (CNAME)
- [ ] SSL certificate provisioned

### 35. Scaling (Optional)
- [ ] Evaluate need for multiple dynos
- [ ] Scale if needed: `heroku ps:scale web=2`
- [ ] Upgrade dyno type if needed

### 36. Monitoring & Alerts (Recommended)
- [ ] Set up Heroku metrics
- [ ] Configure log drain (optional)
- [ ] Set up uptime monitoring
- [ ] Configure error tracking (Sentry, Bugsnag, etc.)

### 37. Backup Strategy
- [ ] Enable automatic database backups
- [ ] Schedule: `heroku pg:backups:schedule`
- [ ] Test restore process
- [ ] Document backup/restore procedures

### 38. Performance Optimization
- [ ] Enable opcode caching (automatic on Heroku)
- [ ] Review slow query logs
- [ ] Optimize database indexes
- [ ] Monitor R2 request times

---

## ✅ FINAL CHECKS

### 39. Smoke Test (Complete Workflow)
- [ ] User registration → Success
- [ ] User login → Success
- [ ] Create folder → Success
- [ ] Upload file → Success
- [ ] Preview file → Success
- [ ] Download file → Success
- [ ] Share file (if feature enabled) → Success
- [ ] Delete file → Success
- [ ] User logout → Success

### 40. Production Readiness
- [ ] All critical features working
- [ ] No known bugs
- [ ] Error handling in place
- [ ] User feedback collected
- [ ] Team trained on Heroku commands
- [ ] Rollback plan in place

---

## 🚨 IF ANYTHING FAILS

### Quick Diagnosis:
```bash
# Check logs
heroku logs --tail

# Check config
heroku config

# Check buildpacks
heroku buildpacks

# Check dynos
heroku ps

# Check database
heroku pg:info
```

### Common Issues:
- **500 Error:** Check logs, verify APP_KEY, clear caches
- **Vite Manifest Missing:** Check buildpack order, rebuild
- **DB Connection Error:** Verify DB_CONNECTION=pgsql
- **R2 Upload Fails:** Test R2 config in tinker
- **Session Issues:** Verify SESSION_DRIVER=database

**See:** `HEROKU_DEPLOYMENT.md` Section 9 for detailed troubleshooting

---

## ✨ DEPLOYMENT COMPLETE!

If all items are checked ✅, your application is:

- ✅ Successfully deployed to Heroku
- ✅ Running in production mode
- ✅ Connected to PostgreSQL database
- ✅ Using Cloudflare R2 for storage
- ✅ Fully functional
- ✅ Production-ready

### Your App:
- **URL:** https://your-app-name.herokuapp.com
- **Status:** Live and operational
- **Stack:** Laravel 11 + Vue 3 + PostgreSQL + R2

**Congratulations! 🎉**

---

## 📞 NEED HELP?

- **Heroku Status:** https://status.heroku.com/
- **View Logs:** `heroku logs --tail`
- **Deployment Guide:** `HEROKU_DEPLOYMENT.md`
- **Quick Reference:** `HEROKU_QUICK_REFERENCE.md`
- **Troubleshooting:** See Section 9 in deployment guide

---

**Checklist Version:** 1.0
**Last Updated:** February 15, 2026
**Application:** HamzaVaultX - Google Drive Clone
