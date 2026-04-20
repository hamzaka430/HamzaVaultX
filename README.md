# HamzaVaultX

> A production-ready cloud storage platform built with modern web technologies

HamzaVaultX is a full-stack SaaS application that provides secure file storage and sharing capabilities, similar to Google Drive. Built with Laravel 11, Vue 3, and Inertia.js, it leverages Cloudflare R2 for scalable object storage and deploys seamlessly on Heroku.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?logo=vue.js)](https://vuejs.org)

## ✨ Features

- 📁 **File Management** - Upload, organize, and manage files in folders
- 🗂️ **Folder Hierarchy** - Create nested folder structures
- 🔗 **File Sharing** - Share files and folders with other users seamlessly
- ⭐ **Favorites** - Star/favorite important files for quick access
- 🗑️ **Trash & Restore** - Soft delete files with a robust recycle bin and restore capability
- 📱 **Mobile Responsive** - Highly optimized UI/UX for all screen sizes (card views on mobile, table views on desktop)
- 👁️ **File Icon Preview** - Automatic icon assignment based on file mime types
- 📥 **Bulk Downloads** - Download multiple files and folders as ZIP
- ☁️ **Cloud Storage** - Cloudflare R2 (S3-compatible) integration for fast, scalable storage

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11
- **Database:** PostgreSQL (Heroku Postgres)
- **Storage:** Cloudflare R2 (S3-compatible)
- **Authentication:** Laravel Sanctum
- **API:** Inertia.js (SPA without REST API)

### Frontend
- **Framework:** Vue 3 (Composition API)
- **Routing:** Inertia.js
- **Styling:** Tailwind CSS
- **Build Tool:** Vite
- **Icons:** Heroicons

### DevOps
- **Platform:** Heroku (heroku-24 stack)
- **Process Manager:** Heroku Procfile
- **Deployment:** Git-based deployment with custom `app.json`

## 🏗️ Architecture Overview

```text
┌──────────────────────────────────────────────┐
│                 Client Layer                 │
│  Vue 3 + Inertia.js + Tailwind CSS + Vite    │
└──────────────────────┬───────────────────────┘
                       │ HTTP/HTTPS
┌──────────────────────▼───────────────────────┐
│              Application Layer               │
│  Laravel 11 + Inertia Server-Side + Sanctum  │
└──────────────────────┬───────────────────────┘
                       │
        ┌──────────────┴──────────────┐
┌───────▼────────┐             ┌──────▼────────┐
│   PostgreSQL   │             │ Cloudflare R2 │
│  (Heroku PG)   │             │(Object Storage)│
└────────────────┘             └───────────────┘
```

## 🚀 Local Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- PostgreSQL 13+ (or MySQL 8+)
- Cloudflare R2 account (or AWS S3)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/hamzaka430/HamzaVaultX.git
   cd HamzaVaultX
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your `.env` file** 
   Set your database credentials and Cloudflare R2 variables.

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start development servers**

   Terminal 1 (Laravel):
   ```bash
   php artisan serve
   ```

   Terminal 2 (Vite):
   ```bash
   npm run dev
   ```

9. **Access the application**
   ```
   http://localhost:8000
   ```

## ☁️ Heroku Deployment

The app is fully configured for Heroku out of the box using `Procfile` and `app.json`.

1. **Login to Heroku CLI**
   ```bash
   heroku login
   ```

2. **Create App & Attach PostgreSQL**
   ```bash
   heroku create hamzavaultx
   heroku addons:create heroku-postgresql:essential-0
   ```

3. **Configure Environment Variables**
   ```bash
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   heroku config:set FILESYSTEM_DISK=r2
   
   # R2 Configuration
   heroku config:set AWS_ACCESS_KEY_ID=your_key
   heroku config:set AWS_SECRET_ACCESS_KEY=your_secret
   heroku config:set AWS_BUCKET=your_bucket
   heroku config:set AWS_ENDPOINT=your_endpoint
   ```

4. **Deploy to Heroku**
   ```bash
   git push heroku main
   ```

5. **Subsequent Deployments**
   ```bash
   npm run build  # ensure assets are built natively before pushing or let Heroku handle it
   git add .
   git commit -m "Deployment update"
   git push heroku main
   ```

## ☁️ Cloudflare R2 Configuration

1. Create a new bucket in the Cloudflare Dashboard (e.g., `hamzavaultx-prod`).
2. Go to **Manage R2 API Tokens** and create a token with **Edit** permissions.
3. Update `.env` or Heroku Config Vars:
   ```env
   AWS_ACCESS_KEY_ID=your_access_key_id
   AWS_SECRET_ACCESS_KEY=your_secret_access_key
   AWS_DEFAULT_REGION=auto
   AWS_BUCKET=hamzavaultx-prod
   AWS_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com
   AWS_USE_PATH_STYLE_ENDPOINT=false
   FILESYSTEM_DISK=r2
   ```

## 💻 Author

**Hamza**

- GitHub: [@hamzaka430](https://github.com/hamzaka430)
- Repository: [HamzaVaultX](https://github.com/hamzaka430/HamzaVaultX)

---

**Built with ❤️ using Laravel, Vue, and Inertia.js**# HamzaVaultX

> A production-ready cloud storage platform built with modern web technologies

HamzaVaultX is a full-stack SaaS application that provides secure file storage and sharing capabilities, similar to Google Drive. Built with Laravel 11, Vue 3, and Inertia.js, it leverages Cloudflare R2 for scalable object storage and deploys seamlessly on Heroku.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?logo=vue.js)](https://vuejs.org)

## ✨ Features

- 🔐 **Secure Authentication** - Laravel Sanctum-powered user authentication
- 📁 **File Management** - Upload, organize, and manage files in folders
- 🗂️ **Folder Hierarchy** - Create nested folder structures
- 🔗 **File Sharing** - Share files and folders with other users
- ⭐ **Favorites** - Star frequently accessed files
- 🗑️ **Trash & Restore** - Soft delete with restore capability
- 📝 **Text Notes** - Create and edit text notes directly in the app
- 👁️ **File Preview** - Preview files before downloading
- 📥 **Bulk Downloads** - Download multiple files and folders as ZIP
- 📊 **Storage Analytics** - Track storage usage per user
- 🌐 **Responsive UI** - Mobile-first design with Tailwind CSS
- ☁️ **Cloud Storage** - Cloudflare R2 (S3-compatible) integration

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11
- **Database:** PostgreSQL (Heroku Postgres)
- **Storage:** Cloudflare R2 (S3-compatible)
- **Authentication:** Laravel Sanctum
- **API:** Inertia.js (SPA without REST API)

### Frontend
- **Framework:** Vue 3 (Composition API)
- **Routing:** Inertia.js
- **Styling:** Tailwind CSS
- **Build Tool:** Vite

### DevOps
- **Platform:** Heroku (heroku-24 stack)
- **Process Manager:** Heroku Procfile
- **CI/CD:** Git-based deployment

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────┐
│                 Client Layer                     │
│  Vue 3 + Inertia.js + Tailwind CSS + Vite       │
└───────────────────┬─────────────────────────────┘
                    │ HTTP/HTTPS
┌───────────────────▼─────────────────────────────┐
│              Application Layer                   │
│  Laravel 11 + Inertia Server-Side + Sanctum     │
└───────────────────┬─────────────────────────────┘
                    │
        ┌───────────┴────────────┐
        │                        │
┌───────▼────────┐    ┌─────────▼──────────┐
│   PostgreSQL   │    │   Cloudflare R2    │
│  (Heroku PG)   │    │  (Object Storage)  │
└────────────────┘    └────────────────────┘
```

## 📸 Screenshots

> Screenshots coming soon

<!-- 
Add your screenshots here:
![Dashboard](docs/screenshots/dashboard.png)
![File Upload](docs/screenshots/upload.png)
![Sharing](docs/screenshots/sharing.png)
-->

## 🚀 Local Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- PostgreSQL 13+ (or MySQL 8+)
- Cloudflare R2 account (or AWS S3)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/hamzaka430/HamzaVaultX.git
   cd HamzaVaultX
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your `.env` file** (see [Environment Variables](#environment-variables))

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start development servers**
   
   Terminal 1 (Laravel):
   ```bash
   php artisan serve
   ```
   
   Terminal 2 (Vite):
   ```bash
   npm run dev
   ```

9. **Access the application**
   ```
   http://localhost:8000
   ```

## 🔐 Environment Variables

### Application Settings
```env
APP_NAME="HamzaVaultX"
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### Database Configuration
```env
# For PostgreSQL (Heroku/Production)
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For MySQL (Local Development)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hamzavaultx
DB_USERNAME=root
DB_PASSWORD=
```

### Cloudflare R2 Storage
```env
FILESYSTEM_DISK=r2

AWS_ACCESS_KEY_ID=your_r2_access_key_id
AWS_SECRET_ACCESS_KEY=your_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your-bucket-name
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Mail Configuration (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@hamzavaultx.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🌐 Production Deployment (Heroku)

### First-Time Setup

1. **Create Heroku app**
   ```bash
   heroku create your-app-name
   ```

2. **Add PostgreSQL addon**
   ```bash
   heroku addons:create heroku-postgresql:essential-0
   ```

3. **Set environment variables**
   ```bash
   heroku config:set APP_NAME="HamzaVaultX"
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   heroku config:set APP_KEY=base64:your-generated-key
   
   # R2 Configuration
   heroku config:set AWS_ACCESS_KEY_ID=your_key
   heroku config:set AWS_SECRET_ACCESS_KEY=your_secret
   heroku config:set AWS_BUCKET=your-bucket
   heroku config:set AWS_ENDPOINT=your-endpoint
   heroku config:set FILESYSTEM_DISK=r2
   ```

4. **Deploy to Heroku**
   ```bash
   git push heroku main
   ```

5. **Run migrations**
   ```bash
   heroku run php artisan migrate --force
   ```

### Subsequent Deployments

```bash
git add .
git commit -m "Your commit message"
git push heroku main
```

### Useful Heroku Commands

```bash
# View logs
heroku logs --tail

# Open app in browser
heroku open

# Run artisan commands
heroku run php artisan tinker

# SSH into dyno
heroku run bash

# Restart dynos
heroku restart
```

## 🗄️ Database Setup

### PostgreSQL (Production)

Heroku automatically provisions and configures PostgreSQL. The `DATABASE_URL` is set automatically.

### MySQL (Local Development)

1. Create database:
   ```sql
   CREATE DATABASE hamzavaultx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_DATABASE=hamzavaultx
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   ```

## ☁️ Cloudflare R2 Configuration

### Step 1: Create R2 Bucket

1. Log in to Cloudflare Dashboard
2. Navigate to **R2 Object Storage**
3. Create a new bucket (e.g., `hamzavaultx-prod`)

### Step 2: Generate API Tokens

1. Go to **R2** → **Manage R2 API Tokens**
2. Create API token with **Edit** permissions
3. Save the `Access Key ID` and `Secret Access Key`

### Step 3: Get Account ID

Find your Account ID in R2 settings or from the endpoint URL format:
```
https://<ACCOUNT_ID>.r2.cloudflarestorage.com
```

### Step 4: Configure Environment

```env
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=hamzavaultx-prod
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
FILESYSTEM_DISK=r2
```

### Test Connection

```bash
php artisan tinker
>>> Storage::disk('r2')->put('test.txt', 'Hello R2');
>>> Storage::disk('r2')->exists('test.txt');
```

## 🏗️ Build Instructions

### Development Build
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

### Build for Heroku
Heroku automatically runs `npm run build` during deployment via the `heroku-postbuild` script in `package.json`.

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 🐛 Troubleshooting

### Issue: 500 Error on Heroku

**Solution:**
```bash
heroku logs --tail
heroku config:set APP_DEBUG=true  # Temporarily for debugging
heroku run php artisan config:clear
heroku restart
```

### Issue: Files not uploading

**Possible causes:**
- R2 credentials incorrect
- `FILESYSTEM_DISK` not set to `r2`
- PHP upload limits too low

**Solution:**
```bash
# Check R2 connection
php artisan tinker
>>> Storage::disk('r2')->exists('test.txt');

# Increase PHP limits (php.ini or .user.ini)
upload_max_filesize = 100M
post_max_size = 100M
max_file_uploads = 100
```

### Issue: Vite manifest not found

**Solution:**
```bash
npm run build
php artisan config:clear
```

### Issue: Database migration fails

**Solution:**
```bash
# Local
php artisan migrate:fresh

# Heroku
heroku run php artisan migrate:fresh --force
```

### Issue: CSS/JS not loading

**Solution:**
- Ensure `npm run build` completed successfully
- Check `public/build/manifest.json` exists
- Verify `APP_URL` is correctly set in `.env`

## 📝 Scripts

```json
{
  "dev": "vite",
  "build": "vite build",
  "heroku-postbuild": "npm run build"
}
```

## 🤝 Contributing

Contributions are welcome! Please check [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Hamza**

- GitHub: [@hamzaka430](https://github.com/hamzaka430)
- Project: [HamzaVaultX](https://github.com/hamzaka430/HamzaVaultX)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - PHP Framework
- [Vue.js](https://vuejs.org) - Frontend Framework
- [Inertia.js](https://inertiajs.com) - Modern Monolith Stack
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS
- [Cloudflare R2](https://www.cloudflare.com/products/r2/) - Object Storage
- [Heroku](https://heroku.com) - Cloud Platform

---

**Built with ❤️ using Laravel, Vue, and Inertia.js**
