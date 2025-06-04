# Distributed Systems Course Project (Hajusrakendused)

A comprehensive Laravel application implementing multiple distributed systems features for the TA23 course project.

**Live Demo**: https://hajus.ta23ruusmann.itmajakas.ee/

## Application Architecture

### Project Structure
```
ta23-hajus/
├── app/                    # Laravel application logic
│   ├── Http/Controllers/   # API and web controllers
│   └── Models/            # Eloquent ORM models
├── resources/
│   ├── js/                # Vue components and pages
│   │   ├── components/    # Reusable UI components
│   │   └── pages/         # Inertia page components
│   └── css/               # Tailwind CSS styles
├── database/
│   ├── migrations/        # Database schema migrations
│   └── seeders/          # Test data generators
├── routes/                # Route definitions
└── public/               # Public directory (document root)
```

### Technology Stack

- **Backend Framework**: Laravel 12 (PHP 8.3)
- **Frontend Framework**: Vue 3 with TypeScript
- **SPA Bridge**: Inertia.js
- **CSS Framework**: Tailwind CSS
- **UI Components**: shadcn-vue (Radix Vue based)
- **Build Tool**: Vite
- **Database**: SQLite (development), MySQL (production)
- **External APIs**: 
  - OpenWeatherMap API (weather data)
  - Stripe API (payment processing)
  - MapLibre GL (interactive maps)

## Running the Application

### Prerequisites
- PHP >= 8.3
- Composer
- Node.js >= 18
- NPM or Yarn
- SQLite or MySQL

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/ta23-hajus.git
   cd ta23-hajus
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set up database**
   ```bash
   # For SQLite
   touch database/database.sqlite
   
   # Run migrations with seed data
   php artisan migrate --seed
   ```

6. **Configure API keys in .env**
   ```env
   WEATHER_API_KEY=your_openweathermap_api_key
   STRIPE_KEY=your_stripe_publishable_key
   STRIPE_SECRET=your_stripe_secret_key
   ```

7. **Start development servers**
   ```bash
   # Run both servers concurrently
   composer run dev
   
   # OR run separately:
   php artisan serve    # Laravel server (http://localhost:8000)
   npm run dev         # Vite dev server
   ```

### Testing
```bash
# Run tests
php artisan test

# Code quality checks
npm run lint
npm run format:check
```

### Production Build
```bash
# Build assets
npm run build

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Overview

This project implements all required features from the [Hajusrakendused course requirements](https://github.com/RalfHei/Hajusrakendused):

### ✅ Completed Features

1. **Weather API Integration** ✓
   - OpenWeatherMap API integration with 30-minute caching
   - City-based weather search
   - Real-time weather data display with icons
   - Error handling for empty searches and invalid cities
   - Responsive weather dashboard

2. **Map Application** ✓
   - Interactive map using MapLibre GL
   - Full CRUD operations for markers
   - Click on map to add new markers
   - Edit and delete functionality for existing markers
   - MySQL-compatible coordinate storage
   - Responsive map interface

3. **Blog System** ✓
   - Full CRUD for posts with authentication
   - Comments system with user association
   - Admin privileges for content management
   - Guest users can view but not interact
   - 30 pre-seeded posts with random users
   - Pagination for post listings

4. **Custom API - Emotions** ✓
   - Database table: `my_favorite_subject` 
   - Display page at /display-subjects with colorful emotion cards
   - Create form for adding new emotions
   - JSON API endpoint at /subjects
   - 6 emotion categories: happy, sad, angry, fear, surprised, love
   - 70 pre-seeded emotions with emojis and colors

5. **E-commerce and Shopping Cart** ✓
   - Product catalog with 12 seeded products
   - Quantity selector on product cards with stock validation
   - Session-based shopping cart with real-time updates
   - Cart management (add, update quantities, remove items)
   - Stripe payment integration with EUR currency
   - Order persistence in database after successful payment
   - Automatic stock updates after purchase
   - Customer information captured from Stripe
   - Responsive design for all screen sizes
   - Error handling and user feedback



## User Access

- **Guest Access**: Browse products, view blog posts, check weather, and see map markers
- **User Registration**: Create an account to access full features (posting, commenting, shopping)
- **Admin Features**: Special privileges for content management (seeded admin account available)

## Stripe Integration

### Setup
1. **Environment Variables**: Add your Stripe keys to `.env`:
   ```
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

2. **Test Card Data for Development**:
   - **Card Number**: `4242 4242 4242 4242`
   - **Expiry**: Any future date (e.g., `12/34`)
   - **CVC**: Any 3 digits (e.g., `123`)
   - **ZIP**: Any 5 digits (e.g., `12345`)

3. **Other Test Cards**:
   - **Requires authentication**: `4000 0025 0000 3155`
   - **Declined**: `4000 0000 0000 9995`
   - **Insufficient funds**: `4000 0000 0000 9995`

### What's Implemented
- ✅ Basic Stripe Checkout integration
- ✅ Redirect to Stripe payment page
- ✅ Success page after payment
- ✅ Cart clears after successful payment
- ✅ Currency set to EUR

## Features

- **City-based Weather Search**: Search for weather by city name
- **Responsive Design**: Mobile-first approach with optimized layouts for all devices
- **Real-time Data**: Integration with OpenWeatherMap API
- **Data Caching**: Implementation reduces API calls and improves performance
- **Error Handling**: Comprehensive error states for API failures
- **Weather Metrics**: Display of temperature, feels-like, humidity, wind speed, pressure, and visibility
- **Weather Icons**: Dynamic weather icons from OpenWeatherMap
- **Theme Integration**: Styled to match application's existing theme system




## Future Enhancement Ideas

### E-commerce Enhancements
- **Guest Checkout**: Allow users to purchase without creating an account
- **Order Management**: Save orders to database with Order & OrderItem models
- **Inventory Management**: Automatically reduce stock after successful purchase
- **Email Confirmations**: Send order confirmation emails after purchase
- **Order History**: Allow users to view their past orders
- **Webhook Integration**: Handle Stripe webhooks for payment confirmation
- **Guest Product Viewing**: Enable non-logged-in users to browse products

### Reading List Feature for Blog
Transform the blog into an interactive reading platform by applying the e-commerce UI patterns:
- **Visual Enhancement**: Use the same card-based grid layout for blog posts with featured images
- **"Add to Reading List"** button overlay on post images (similar to cart functionality)
- **Personal Reading Lists**: Allow users to curate their own reading collections
- **Reading Progress Tracking**: Mark posts as read/unread, track reading time
- **Session-based Storage**: Start with simple session storage, upgrade to user-specific lists
- **Additional Features**:
  - Estimated reading time display
  - Reading statistics and insights
  - Share reading lists with others
  - Bookmarks within posts
  - Reading recommendations based on history

This enhancement would elevate the blog from a basic CRUD system to an engaging reading platform, maximizing code reuse from the e-commerce components.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
