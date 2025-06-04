# 🚀 GitHub → Zone.ee Auto-Deployment Guide

## 📋 What We're Building
**Goal**: Push code to GitHub → Auto-deploys to Zone.ee

### The Magic Flow:
1. You push code to GitHub
2. GitHub Actions runs automatically
3. Your site updates on Zone.ee
4. No manual uploads needed! 🎉

**🛑 STOP HERE - Make sure you understand the goal!**

---

## 🔑 Step 1: SSH Key Setup
**Goal**: Create secure connection between GitHub and Zone.ee

### First, Check If You Have SSH Keys:
- [x] Open terminal
- [x] Run: `ls ~/.ssh/id_*.pub`
- [x] See files listed? **Skip to "Use Existing Keys" below** ✅
- [ ] No files? **Continue to "Create New Keys"** 👇

### Create New Keys (only if needed):
- [ ] Run: `ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519_projectname -N ''`
- [ ] Replace "projectname" with your actual project name
- [ ] You now have 2 files:
  - [ ] Private key: `~/.ssh/id_ed25519_projectname`
  - [ ] Public key: `~/.ssh/id_ed25519_projectname.pub`

### Use Existing Keys:
- [ ] Run: `cat ~/.ssh/id_ed25519_projectname` → Copy this (PRIVATE - keep secret!)
- [ ] Run: `cat ~/.ssh/id_ed25519_projectname.pub` → Copy this (PUBLIC - can share)
- [ ] 💡 **Note**: Replace "projectname" with your actual key name!

**🛑 STOP HERE - Keys ready! Take a break ☕**

---

## 🔐 Step 2: Zone.ee SSH Setup
**Goal**: Tell Zone.ee to trust GitHub

### In Zone.ee Control Panel:
- [x] Find "SSH Access" or "SSH Keys"
- [x] Add new SSH key
- [x] Paste your PUBLIC key (the .pub one)
- [x] Name it: "GitHub Deploy"
- [x] Save

### Test Connection:
- [ ] In terminal: `ssh your-username@your-server.zone.ee`
- [ ] Should connect without password
- [ ] Type `exit` to disconnect

**🛑 STOP HERE - Zone.ee ready!**

---

## 🐙 Step 3: GitHub Repository Setup
**Goal**: Add deploy key to your repo

### In Your GitHub Repo:
- [ ] Go to Settings → Deploy keys (NOT personal SSH keys!)
- [ ] Click "Add deploy key"
- [ ] Title: "Zone.ee ProjectName Deployment"
- [ ] Key: Paste your PUBLIC key (.pub file)
- [ ] ✅ Allow write access (if needed)
- [ ] Add key

### If Key Already Exists:
- [ ] Create a new key with different name (Step 1)
- [ ] Use project-specific keys to avoid conflicts

**🛑 STOP HERE - GitHub configured!**

---

## 🤫 Step 4: GitHub Secrets
**Goal**: Store connection info securely

### Add These Secrets (Settings → Secrets → Actions):
- [ ] `HOST` → your-server.zone.ee
- [ ] `USERNAME` → your-zone-username
- [ ] `SSH_KEY` → Your PRIVATE key (the one without .pub)
- [ ] `DEPLOY_PATH` → /path/to/your/project

### Optional Secrets:
- [ ] `DB_PASSWORD` → Your database password
- [ ] `WEATHER_API_KEY` → Your weather API key

**🛑 STOP HERE - Secrets stored safely!**

---

## 📝 Step 5: Create Deployment Script
**Goal**: Tell GitHub what to do

### Create `.github/workflows/deploy.yml`:
- [ ] In your project, create folders: `.github/workflows/`
- [ ] Create file: `deploy.yml`
- [ ] Copy this:

```yaml
name: 🚀 Deploy to Zone.ee

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: 🏗 Checkout code
      uses: actions/checkout@v4
    
    - name: 🚀 Deploy to server
      uses: appleboy/ssh-action@v1.0.0
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd ${{ secrets.DEPLOY_PATH }}
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm ci
          npm run build
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan migrate --force
```

**🛑 STOP HERE - Workflow created!**

---

## 🏃 Step 6: Initial Server Setup
**Goal**: Prepare Zone.ee for auto-deployment

### Configure SSH for GitHub:
```bash
# Create SSH config file
nano ~/.ssh/config
```

Add these lines:
```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_projectname
```

### Connect via SSH:
```bash
ssh username@server.zone.ee
```

### Run These Commands:
- [ ] Test GitHub connection: `ssh -T git@github.com`
- [ ] Navigate to web directory: `cd /data01/virt123071/domeenid/www.yourdomain.ee`
- [ ] Remove old files if needed: `rm -rf projectname`
- [ ] Clone repository: `git clone git@github.com:yourusername/yourrepo.git projectname`
- [ ] Enter project: `cd projectname`
- [ ] Create `.env` file (copy from local or backup)
- [ ] Install dependencies: `composer install --no-dev`
- [ ] Generate key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Set permissions: `chmod -R 755 storage bootstrap/cache`

**🛑 STOP HERE - Server ready!**

---

## 🎯 Step 7: Test Deployment
**Goal**: Make sure it works!

### Make a Test Change:
- [ ] Edit README.md (add a line)
- [ ] Commit: `git commit -am "Test deployment"`
- [ ] Push: `git push origin main`

### Watch the Magic:
- [ ] Go to GitHub → Actions tab
- [ ] See your workflow running
- [ ] Wait for green checkmark ✅
- [ ] Check your Zone.ee site - updated!

**🎉 AUTO-DEPLOYMENT ACTIVE! 🎉**

---

## 🚨 Troubleshooting

### Deployment Failed?
- Check GitHub Actions logs (click the failed run)
- Common issues:
  - [ ] Wrong SSH key
  - [ ] Wrong server path
  - [ ] Missing npm/composer on server

### Permission Errors?
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Can't Connect via SSH?
- [ ] Check Zone.ee allows SSH access
- [ ] Verify SSH key is added
- [ ] Try password login first

---

## 🔄 Daily Workflow Now

### Just This:
1. Make changes locally
2. `git add .`
3. `git commit -m "Your message"`
4. `git push`
5. ✨ Site updates automatically!

### No More:
- ❌ FTP uploads
- ❌ Manual file copying
- ❌ Forgetting which files changed
- ❌ Version mismatches

---

## 🎯 Quick Reference

### Your Secrets Should Be:
- `HOST`: d123460.zone.ee (example)
- `USERNAME`: d123460 (example)
- `SSH_KEY`: -----BEGIN OPENSSH PRIVATE KEY-----...
- `DEPLOY_PATH`: /data01/virt123460/domeenid/www.example.ee/laravel-app

### Essential Commands:
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "github-deploy"

# Test SSH connection
ssh username@server.zone.ee

# View your keys
cat ~/.ssh/id_ed25519      # Private
cat ~/.ssh/id_ed25519.pub  # Public
```

**You've automated deployment! 🚀 No more manual uploads!**