# 🚀 Deployment Readiness Checklist

## ✅ Step 1: Build Assets
**Goal**: Make sure frontend compiles

- [x] Run: `npm run build`
- [x] ✓ Should see "build complete" message
- [x] ✓ No red errors

**🛑 STOP HERE - Take a break if needed!**

---

## 📦 Step 2: Check Dependencies
**Goal**: Ensure all packages are production-ready

- [x] Run: `composer install --no-dev`
- [x] ✓ Should complete without errors
- [x] Run: `composer install` (to restore dev dependencies for now)

**🛑 STOP HERE - You're doing great!**

---

## 🔧 Step 3: Environment Check
**Goal**: Make sure secret keys exist

- [x] Check weather API key exists:
  -[x] Run: `grep WEATHER_API_KEY .env`
  -[x] ✓ Should show your key
- [x] Check Stripe keys exist:
  - [x] Run: `grep STRIPE_KEY .env`
  - [x] ✓ Should show your key
  - [x] Run: `grep STRIPE_SECRET .env`
  - [x] ✓ Should show your secret

**🛑 STOP HERE - Almost done!**

---

## 🧪 Step 4: Run Tests
**Goal**: Make sure nothing is broken

- [x] Run: `php artisan test`
- [x] ✓ All tests should pass (green)
- [x] 🟡 If any fail, we'll fix them

**🛑 STOP HERE - Great progress!**

---

## 🔍 Step 5: Clean Code Check
**Goal**: Remove debug code

- [x] Search for debug statements:
  - [x] Run: `grep -r "dd(" app/ | wc -l`
  - [x] ✓ Should show 0
  - [x] Run: `grep -r "console.log" resources/js/ | wc -l`
  - [x] ✓ Should show 0 (or only necessary ones)

**🛑 STOP HERE - Final stretch!**

---

## 📂 Step 6: File Permissions
**Goal**: Ensure Laravel can write files

- [x] Check storage is writable:
  - [x] Run: `ls -la storage/`
  - [x] ✓ Should see directories
- [x] Check cache is writable:
  - [x] Run: `ls -la bootstrap/cache/`
  - [x] ✓ Should see cache files

**🛑 STOP HERE - You did it!**

---

## 🎉 Step 7: Production Test
**Goal**: Test with production settings

- [x] Make backup: `cp .env .env.backup`
- [x] Edit .env:
  - [x] Change `APP_ENV=local` to `APP_ENV=production`
  - [x] Change `APP_DEBUG=true` to `APP_DEBUG=false`
- [x] Clear cache: `php artisan config:clear`
- [x] Test site still works: `php artisan serve`
- [x] Restore settings: `cp .env.backup .env`
- [x] Clear cache again: `php artisan config:clear`

**✨ DEPLOYMENT READY! ✨**

---

## 🎯 Quick Version (If Overwhelmed)
Just run these 3 commands:
1. `npm run build` - ✓ No errors?
2. `php artisan test` - ✓ All green?
3. `grep -r "dd(" app/` - ✓ No results?

**You're ready to deploy!**

---

## 🚢 Step 8: Deploy to Zone.ee
**Goal**: Get your code on the server

### Option A: GitHub Clone (Recommended)
- [ ] SSH to server: `ssh virt123071@ta23ruusmann.itmajakas.ee`
- [ ] Clone from GitHub: `git clone https://github.com/yourusername/yourrepo.git`
- [ ] Copy .env file from backup
- [ ] Run: `composer install --no-dev`
- [ ] Run: `npm install && npm run build` (or upload build folder)
- [ ] Set permissions: `chmod -R 755 storage bootstrap/cache`
- [ ] Run: `php artisan key:generate` (if new .env)
- [ ] Run: `php artisan migrate --force`
- [ ] Run: `php artisan config:cache`

### Option B: Manual Upload
- [ ] Create zip without: node_modules, .env, .git
- [ ] Upload via WebFTP
- [ ] Extract files
- [ ] Create .env file
- [ ] Set permissions
- [ ] Run artisan commands via SSH

**🎉 SITE IS LIVE! 🎉**