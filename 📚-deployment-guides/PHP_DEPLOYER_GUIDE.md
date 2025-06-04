# 🚀 PHP Deployer Setup Guide for Zone.ee

## 📋 What is PHP Deployer?
**Goal**: One-command deployment to Zone.ee server

### Why Use Deployer?
- ✅ Deploy with just `dep deploy`
- ✅ Automatic rollback if something fails
- ✅ Zero-downtime deployments
- ✅ Keep multiple releases for easy rollback
- ✅ No more manual FTP uploads!

**🛑 STOP HERE - Make sure you understand the benefits!**

---

## 📦 Step 1: Install Deployer
**Goal**: Add Deployer to your project

### Install as Dev Dependency:
- [x] Run: `composer require --dev deployer/deployer`
- [x] ✓ Should see "Package operations: X installs"
- [x] ✓ Check it's installed: `vendor/bin/dep --version`

### Alternative Global Install:
- [ ] Run: `curl -LO https://deployer.org/deployer.phar`
- [ ] Run: `mv deployer.phar /usr/local/bin/dep`
- [ ] Run: `chmod +x /usr/local/bin/dep`
- [ ] ✓ Check: `dep --version`

**🛑 STOP HERE - Deployer installed! Take a break ☕**

---

## 🔧 Step 2: Create Configuration
**Goal**: Tell Deployer about your server

### Create deploy.php:
- [x] In project root, create `deploy.php`
- [x] Add this starter config:

```php
<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'ta23-hajus');

// Project repository
set('repository', 'git@github.com:yourusername/ta23-hajus.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', ['.env']);
add('shared_dirs', [
    'storage',
    'bootstrap/cache',
]);

// Writable dirs by web server 
add('writable_dirs', [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
]);

// Hosts
host('zone.ee')
    ->setHostname('ta23ruusmann.itmajakas.ee')
    ->setRemoteUser('virt123071')
    ->setDeployPath('/data01/virt123071/domeenid/www.ta23ruusmann.itmajakas.ee/hajus-ta23')
    ->setIdentityFile('~/.ssh/id_ed25519');

// Tasks
task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

// Run build task locally before deploy
before('deploy:symlink', 'build');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
```

**🛑 STOP HERE - Basic config ready!**

---

## 🔑 Step 3: SSH Setup
**Goal**: Ensure Deployer can connect to Zone.ee

### Check Existing SSH Key:
- [x] Run: `ls ~/.ssh/id_*`
- [x] Have a key? Skip to "Test Connection"
- [ ] No key? Continue below

### Generate SSH Key (if needed):
- [ ] Run: `ssh-keygen -t ed25519 -C "deployer@ta23"`
- [ ] Press Enter for default location
- [ ] Press Enter for no passphrase
- [ ] ✓ Created: `~/.ssh/id_ed25519` and `~/.ssh/id_ed25519.pub`

### Add Key to Zone.ee:
- [x] Copy public key: `cat ~/.ssh/id_ed25519.pub`
- [x] Login to Zone.ee control panel
- [x] Go to: SSH Access → Add SSH Key
- [x] Paste the public key
- [x] Name: "Deployer Key"
- [x] Save

### Test Connection:
- [x] Run: `ssh virt123071@ta23ruusmann.itmajakas.ee`
- [x] ✓ Should connect without password
- [x] Type `exit` to disconnect

**🛑 STOP HERE - SSH configured!**

---

## 📂 Step 4: Server Preparation
**Goal**: Set up deployment structure on Zone.ee

### Connect to Server:
```bash
ssh virt123071@ta23ruusmann.itmajakas.ee
```

### Prepare Deployment Directory:
- [x] Navigate to web root: `cd /data01/virt123071/domeenid/www.ta23ruusmann.itmajakas.ee`
- [x] Create deployment folder: `mkdir -p hajus-ta23`
- [x] Enter folder: `cd hajus-ta23`
- [x] Create shared folder: `mkdir -p shared`
- [x] Create .env file: `nano shared/.env`
- [x] Paste your production .env content
- [x] Save and exit: Ctrl+X, Y, Enter

### Set Permissions:
- [x] Run: `chmod -R 755 shared`
- [x] Exit SSH: `exit`

**🛑 STOP HERE - Server prepared!**

---

## 🚀 Step 5: First Deployment
**Goal**: Deploy your application!

### Initialize Deployment:
- [x] Run: `dep deploy:setup`
- [x] ✓ Should create deployment structure

### Deploy Application:
- [ ] Run: `dep deploy`
- [ ] ✓ Watch the magic happen!
- [ ] ✓ Should see "Successfully deployed!"

### If Deploy Fails:
- [ ] Check error message
- [ ] Run: `dep deploy:unlock` (if locked)
- [ ] Fix issues and try again

### After First Deployment:
- [ ] Seed the database (requires dev dependencies):
  ```bash
  # SSH to server
  dep ssh
  
  # Navigate to current release
  cd current
  
  # Install dev dependencies temporarily
  composer install
  
  # Run seeders
  php artisan db:seed
  
  # Remove dev dependencies for production
  composer install --no-dev --optimize-autoloader
  
  # Exit SSH
  exit
  ```
- [ ] ✓ Creates test users, posts, and other sample data
- [ ] ✓ Default users: test@test.test and admin@test.test

**🎉 DEPLOYED WITH ONE COMMAND! 🎉**

---

## 🔄 Step 6: Daily Workflow
**Goal**: Understand your new workflow

### To Deploy Updates:
```bash
# Make your changes
git add .
git commit -m "Your changes"
git push

# Deploy to production
dep deploy
```

### Useful Commands:
- [ ] `dep deploy` - Deploy latest code
- [ ] `dep rollback` - Rollback to previous release
- [ ] `dep ssh` - SSH into server
- [ ] `dep artisan:migrate` - Run migrations only
- [ ] `dep artisan:db:seed` - Run database seeders
- [ ] `dep deploy:unlock` - Unlock if deployment failed

**🛑 STOP HERE - You know the basics!**

---

## 🎯 Step 7: Advanced Configuration
**Goal**: Customize for your needs

### Add Custom Tasks:
```php
// Clear all Laravel caches
task('artisan:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
    run('{{bin/php}} {{release_path}}/artisan config:clear');
    run('{{bin/php}} {{release_path}}/artisan route:clear');
    run('{{bin/php}} {{release_path}}/artisan view:clear');
})->desc('Clear all Laravel caches');

// Run after deployment
after('deploy:symlink', 'artisan:cache:clear');
```

### Skip Dev Dependencies:
```php
// Install only production packages
set('composer_options', '--no-dev --optimize-autoloader');
```

### Keep More Releases:
```php
// Keep last 5 releases for rollback
set('keep_releases', 5);
```

**🛑 STOP HERE - Advanced features added!**

---

## 🚨 Troubleshooting

### Can't Connect to Server?
- Check SSH key is added to Zone.ee
- Verify hostname and username in deploy.php
- Try manual SSH first

### Permission Denied?
```bash
dep ssh
cd {{deploy_path}}/shared
chmod -R 755 storage
chmod 644 .env
```

### Composer Memory Limit?
Add to deploy.php:
```php
set('php', 'php -d memory_limit=512M');
```

### NPM Not Found?
Install Node.js on server or build locally:
```php
task('build:local', function () {
    runLocally('npm install');
    runLocally('npm run build');
    upload('public/build/', '{{release_path}}/public/');
})->desc('Build assets locally');

// Use local build instead
before('deploy:symlink', 'build:local');
```

### Database Seeding Error (Faker Not Found)?
Faker is a dev dependency. To seed in production:
```bash
dep ssh
cd current
composer install                    # Install all dependencies
php artisan db:seed                 # Run seeders
composer install --no-dev --optimize-autoloader  # Remove dev dependencies
exit
```

---

## 📋 Quick Reference

### Essential Commands:
```bash
# First time setup
dep deploy:setup

# Deploy application
dep deploy

# Rollback if needed
dep rollback

# SSH to server
dep ssh

# Run specific task
dep artisan:migrate
dep artisan:db:seed
dep artisan:cache:clear
```

### Deployment Flow:
1. `dep deploy` runs these steps:
   - Connect to server
   - Clone/pull latest code
   - Install composer dependencies
   - Create new release folder
   - Copy shared files (.env)
   - Link shared directories (storage)
   - Build assets
   - Run migrations
   - Switch symlink (zero downtime!)
   - Clean old releases

### File Structure on Server:
```
hajus-ta23/
├── current -> releases/3  (symlink to active release)
├── releases/
│   ├── 1/
│   ├── 2/
│   └── 3/  (latest release)
└── shared/
    ├── .env
    └── storage/
```

---

## 🎯 Migration Checklist

### From Manual Deployment:
- [x] Install Deployer
- [x] Create deploy.php
- [x] Configure host settings
- [x] Add SSH key to Zone.ee
- [x] Create shared/.env on server
- [x] Run `dep deploy:setup`
- [x] Run `dep deploy`
- [ ] Delete old manual files (optional)

### From GitHub Actions:
- [x] Keep your GitHub workflow as backup
- [x] Use Deployer for regular deploys
- [x] GitHub Actions for CI/CD testing

**🚀 Welcome to One-Command Deployments!**