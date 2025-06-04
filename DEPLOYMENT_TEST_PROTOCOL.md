# 🧪 Deployment Test Protocol - TA23 Hajusrakendused

**URL:** https://hajus.ta23ruusmann.itmajakas.ee/  
**Date:** 04.06.2025  
**Status:** Round 2 - After Fixes

## ✅ Basic Checks
- [x] Site loads
- [x] No error pages
- [x] CSS looks normal
- [x] Favicon appears ✅ FIXED
- [x] Title shows "TA23 Hajusrakendused" ✅ FIXED

---

## 1️⃣ Weather Service API Integration

### Quick Test
- [x] Search "Tallinn" → Shows weather
- [x] Search "London" → Shows weather
- [x] Has temperature, icon, description

### Full Requirements Test
- [x] OpenWeatherMap API connected
- [x] Weather data retrieved in JSON format
- [x] Local data caching implemented (30 min cache)
- [x] Visual elements displayed (icons, temp, etc)
- [x] Search for different cities works
- [ ] ❌ Empty search handling (breaks UI)

---

## 2️⃣ Map Application with Markers

### Quick Test
- [x] Map page loads
- [x] Can see markers on map ✅ FIXED (model casts)
- [x] Can click markers → Shows info

### Full Requirements Test
- [x] MapLibre (OpenStreetMap) API integrated
- [x] Database table `markers` exists with all fields:
  - [x] id, name, latitude, longitude, description, added (created_at), edited (updated_at)
- [x] CRUD operations implemented:
  - [x] Create marker by clicking map
  - [x] Read/View all markers on map
  - [x] Update marker (edit form)
  - [x] Delete marker
- [x] All markers shown on map view
- [x] Click on map → Add marker form

---

## 3️⃣ Blog and Comment Management

### Quick Test
- [x] Posts page shows list
- [x] Can open a post
- [x] Comments visible under posts
- [x] Login with test@test.test / password
- [x] Can create new post (if logged in)

### Full Requirements Test
- [x] Blog functionality created
- [x] Authentication system (login/register)
- [x] Posts table with required fields:
  - [x] id, title, description, created_at, updated_at
- [x] Full CRUD for blog posts:
  - [x] Create new post (authenticated)
  - [x] Read/View posts (public)
  - [x] Update own posts
  - [x] Delete own posts
- [x] Comments on posts work
- [x] Admin can delete any comment (admin@test.test)
- [ ] ⚠️ Comment button hidden for non-logged users

---

## 4️⃣ E-commerce and Shopping Cart

### Quick Test
- [x] Products page shows items with prices (€)
- [x] Add to cart → Cart count updates
- [x] Cart page → Shows items and total
- [x] Checkout → Goes to Stripe
- [x] Test card: 4242 4242 4242 4242

### Full Requirements Test
- [x] Product page with 12 products (exceeds 9 minimum)
- [x] Each product shows: image, name, price, description
- [ ] Quantity selection on product page
- [x] Shopping cart functionality:
  - [x] Add products to cart
  - [x] Modify quantities in cart (+/-)
  - [x] Remove products from cart
- [x] Checkout page (via Stripe) collects:
  - [x] Email (Stripe handles other details)
- [x] Stripe payment integration
- [x] Success page after payment
- [x] Cart clears on successful payment
- [ ] Error handling for failed payments
- [ ] Order data saved to database
- [ ] ⚠️ Console errors with price.toFixed

---

## 5️⃣ Custom API - Emotions/Subjects

### Quick Test
- [x] /display-subjects → Shows colorful cards
- [x] /subjects → Shows JSON data
- [x] Can add new emotion (if form exists)

### Full Requirements Test
- [x] Topic selected: Emotions
- [x] Table `my_favorite_subject` with fields:
  - [x] id, title, description
  - [ ] image field (not used)
  - [x] Additional fields: emoji, color, category, intensity
- [x] User-friendly data entry form
- [x] JSON API at /subjects endpoint
- [ ] ❌ API filtering/sorting missing
- [ ] ❌ API search functionality missing
- [ ] ❌ API limit results missing
- [x] Webpage to browse content (/display-subjects)
- [ ] ❓ Data caching implementation

---

## 🎯 Final Test Results

### ✅ Working Features
1. **Weather**: API integration, caching (30min), city search, visual display
2. **Map**: View markers, add markers, delete markers, click for info
3. **Blog**: Full CRUD, authentication, comments, admin controls
4. **Shop**: Product display, cart management, Stripe payments (success flow)
5. **API**: JSON endpoint, data entry form, display page

### ❌ Issues Found
1. **Weather**: Empty search breaks UI completely
2. **Map**: Edit marker returns Error 500, delete button text invisible (red on red)
3. **Blog**: Comment button visible to guests (causes server error)
4. **Shop**: 
   - No quantity selector on product page
   - Orders not saved to database
   - Failed payments cause Error 419
   - Console errors with price.toFixed
5. **API**: No search/filter/limit/caching (all were optional)

### 📊 Requirements Score
- **Weather**: 5/6 requirements met (83%)
- **Map**: 8/9 requirements met (89%)
- **Blog**: 9/10 requirements met (90%)
- **Shop**: 6/10 requirements met (60%)
- **API**: 4/8 requirements met (50% - but optional features)

### Grade Assessment
- **Current**: Strong Grade 3 (all basic requirements met)
- **Missing for Grade 4**: 
  - Fix all bugs (500 errors, UI breaks)
  - Better error handling
  - Complete e-commerce features
- **Missing for Grade 5**: 
  - API advanced features
  - Performance optimization
  - Perfect UX/UI

**Testing Completed:** 04.06.2025  
**Total Testing Time:** 60 minutes  
**Tester:** Anne Ruusmann