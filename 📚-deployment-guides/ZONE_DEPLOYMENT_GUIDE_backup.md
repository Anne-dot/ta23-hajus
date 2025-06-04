# 🚀 Zone.ee Deployment Guide

## 📋 Pre-Deployment Prep
**Goal**: Get files ready for upload

- [ x Create deployment folder on your computer
- [ ] Copy your project files (exclude these):
  - [x] ❌ Don't copy `node_modules/`
  - [x] ❌ Don't copy `.env`
  - [x] ❌ Don't copy `database/*.sqlite`
  - [x] ❌ Don't copy `.git/`
  - [x] ✅ Copy everything else

**🛑 STOP HERE - Take a break if needed!**

---

## 🗄️ Step 1: Database Setup
**Goal**: Create MySQL database on Zone.ee

- [x] Login to Zone.ee control panel
- [x] Find "MySQL Databases" section
- [x] Click "Create Database"
- [x] Save these details:
  - [x] Database name: d123460_hajusta23
  - [x] Username: hajuskasutaja
  - [x] Password: ujeshtuafsdnsklRR2346jndcj?_
  - [x] Host: d123460.mysql.zonevs.eu

**🛑 STOP HERE - Good progress!**

---

## 📤 Step 2: Upload Files
**Goal**: Get your files on the server

- [x] Open FTP client (or Zone.ee file manager)
- [x] Connect to your Zone.ee account
- [x] Navigate to your domain folder
- [x] Upload all files (this takes time - get coffee ☕)
- [x] ✓ Check files are uploaded

### Important: Check for development files
- [ ] After extracting, delete these files if they exist:
  - [ ] `public/hot` (causes Vite to load from localhost)
  - [ ] `database/*.sqlite` (local database files)

**🛑 STOP HERE - Halfway there!**

---

## 🔧 Step 3: Configure Environment
**Goal**: Set up .env file

- [x] In WebFTP, navigate into your project folder (e.g., `hajus-ta23`)
- [x] Create new file: `.env` (use "New File" or "Create File" button)
- [x] Edit the `.env` file and paste:
  ```
  APP_NAME="TA23 Hajusrakendused"
  APP_ENV=production
  APP_DEBUG=false
  APP_KEY=
  APP_URL=https://ta23ruusmann.itmajakas.ee/hajus-ta23
  
  DB_CONNECTION=mysql
  DB_HOST=d123460.mysql.zonevs.eu
  DB_PORT=3306
  DB_DATABASE=d123460_hajusta23
  DB_USERNAME=d123460_hajuskasutaja
  DB_PASSWORD=[your_password_from_step1]
  
  WEATHER_API_KEY=[your_weather_key]
  STRIPE_KEY=[your_stripe_key]
  STRIPE_SECRET=[your_stripe_secret]
  ```
- [x] Fill in your actual API keys and database password
- [x] Leave APP_KEY empty (will generate later)
- [x] Save the file

**🛑 STOP HERE - Almost done!**

---

## 🔐 Step 4: Set Permissions
**Goal**: Make Laravel happy with permissions

Using Zone.ee File Manager:

### For `storage/` and `bootstrap/cache/` folders (755):
- [x] Right click folder → Permissions/CHMOD
- [x] Set permissions:
  - **Owner**: ✓ Read, ✓ Write, ✓ Execute
  - **Group**: ✓ Read, ✗ Write, ✓ Execute  
  - **Public**: ✓ Read, ✗ Write, ✓ Execute
  - **OR** enter `755` in the numeric field
- [x] Apply to all subfolders and files

### Create required cache directories:
- [ ] Run these commands in SSH or create via file manager:
  ```bash
  mkdir -p storage/framework/cache/data
  mkdir -p storage/framework/sessions
  mkdir -p storage/framework/views
  ```
- [ ] Set permissions on these new directories to 755

### For `.env` file (644):
- [x] Set permissions:
  - **Owner**: ✓ Read, ✓ Write, ✗ Execute
- [x] Right click `.env` → Permissions/CHMOD
  - **Group**: ✓ Read, ✗ Write, ✗ Execute
  - **Public**: ✓ Read, ✗ Write, ✗ Execute
  - **OR** enter `644` in the numeric field

**🛑 STOP HERE - Great job!**

---

## 📁 Step 5: Create Subdomain for Laravel
**Goal**: Set up a subdomain pointing to Laravel's public folder

### Navigate to Subdomain Settings:
- [x] In Zone.ee control panel → Services Overview → Webhosting
- [x] Click "Webserver" → "Subdomains"
- [x] Click "Add subdomain" button

### Configure the Subdomain:
- [x] **Name**: Enter your subdomain (e.g., `hajus` for hajus.ta23ruusmann.itmajakas.ee)
- [x] **Directory on server**: Will auto-fill like `/data01/virt123071/domeenid/www.ta23ruusmann.itmajakas.ee/hajus`
- [x] **IMPORTANT**: Manually add `/public` at the end of the path!
- [x] **Uncheck** "Create subdomain directory automatically"
- [x] Click Create/Save

### After Creation:
- [x] SSL certificate: Select from dropdown if not auto-assigned
- [x] Set **PHP mode** to PHP 8.2 or 8.3
- [x] Leave other settings as default
- [x] Save changes

**🛑 STOP HERE - Final steps ahead!**

---

## 🎯 Step 6: Run Laravel Commands
**Goal**: Set up the application

Using Zone.ee SSH (if available) or their web terminal:
- [x] Run: `php artisan key:generate`
- [x] Run: `php artisan migrate --force`
- [ ] Run: `php artisan db:seed --force` (optional - adds test data)
- [ ] Run: `php artisan config:cache`
- [ ] Run: `php artisan route:cache`
- [ ] Run: `php artisan view:cache`

If no SSH access, look for "Artisan Commands" in control panel.

**🛑 STOP HERE - One more step!**

---

## ✅ Step 7: Test Everything
**Goal**: Make sure it works!

- [ ] Visit your site: https://yourdomain.zone.ee
- [ ] Check homepage loads
- [ ] Test login/register
- [ ] Check products page
- [ ] Test adding to cart
- [ ] Verify weather widget works

**🎉 DEPLOYED! 🎉**

---

## 🚨 Troubleshooting

### White Screen/500 Error?
1. Check `.env` file exists and has correct values
2. Check file permissions (storage = 755)
3. Check `.htaccess` exists in `/public`

### Database Error?
1. Verify database credentials in `.env`
2. Make sure you ran `php artisan migrate`

### Assets Not Loading?
1. Make sure you uploaded the `public/build/` folder
2. Check APP_URL in `.env` matches your domain

### Still Stuck?
- Check Zone.ee error logs
- Contact Zone.ee support

### Vite Loading from Localhost?
If you see errors about http://127.0.0.1:5173:
1. Delete the `public/hot` file
2. Make sure APP_ENV=production in .env
3. Clear config cache: `php artisan config:clear`

### "Please provide a valid cache path" Error?
1. Create missing directories:
   ```bash
   mkdir -p storage/framework/cache/data
   mkdir -p storage/framework/sessions
   mkdir -p storage/framework/views
   chmod -R 755 storage
   ```

### Using Subdomains vs Subdirectories?
- **Subdomain** (e.g., hajus.ta23ruusmann.itmajakas.ee):
  - Set APP_URL to full subdomain URL
  - Point DocumentRoot to `/your-project/public`
- **Subdirectory** (e.g., ta23ruusmann.itmajakas.ee/hajus):
  - Create symlink in htdocs
  - Set APP_URL with subdirectory path

---

## 🎯 Quick Checklist
If overwhelmed, just make sure:
- [ ] Database created
- [ ] Files uploaded
- [ ] Delete `public/hot` file if it exists
- [ ] .env configured
- [ ] Permissions set (storage = 755)
- [ ] Ensure cache directories exist
- [ ] Migrations run

**You've got this! 💪**