# Distributed Systems Course Project (Hajusrakendused)

A comprehensive Laravel application implementing multiple distributed systems features for the TA23 course project.

## Project Overview

This project implements the required features from the [Hajusrakendused course requirements](https://github.com/RalfHei/Hajusrakendused):

### ✅ Completed Features

1. **Weather API Integration** ✓
   - OpenWeatherMap API integration with caching
   - Responsive weather dashboard component
   - Real-time weather data display

2. **Map Application** ✓
   - Interactive map using MapLibre GL
   - Marker management (CRUD operations)
   - Coordinate tracking and display

3. **Blog System** ✓
   - Basic CRUD for posts implemented
   - Comments system implemented (using "Commetn" table)
   - Authentication middleware added
   - Post factory and seeder created (30 posts with random users)
   - Users can manage their own posts and comments
   - Admin can manage all posts and comments

4. **Custom API - Emotions** ✓
   - Database table: `my_favorite_subject` (follows course requirement)
   - Display page implemented (/display-subjects)
   - Grid layout with colorful cards
   - Create form with working POST functionality
   - Intensity input using number field (1-10)
   - JSON API endpoint at /subjects
   - Categories: happy, sad, angry, fear, surprised, love

### 🚧 In Progress

5. **E-commerce and Shopping Cart** (Development Started)
   
   **✅ Completed:**
   - `Product` model with migration, factory, and seeder
     - Fields: id, name, price, description, image, quantity, timestamps
     - Factory generates random product data with picsum images
     - Seeder creates 12 test products
   - `ProductController` with `index()` method
   - Product catalog route at `/products`
   - Basic Inertia view setup for products display
   
   **🔄 In Progress:**
   - Products index Vue page with grid layout
   
   **📋 TODO:**
   - Style products grid (3-4 columns responsive)
   - Add "Add to Cart" buttons
   - `CartController` - Full CRUD for cart management
   - `CheckoutController` - Handle checkout flow and Stripe integration
     - `create()` - Show checkout form (name, email, phone)
     - `store()` - Process payment with Stripe
     - `success()` - Handle successful payment (clear cart, save order)
     - `cancel()` - Handle cancelled/failed payment (keep cart)
   - `Order` model - Store completed orders
   - `OrderItem` model - Store order line items
   - Shopping cart functionality (add, update quantity, remove items)
   - Session-based cart storage for MVP
   - Checkout page with user data collection
   - Payment integration with Stripe (test mode)
   - Post-payment actions (clear cart on success, retain on failure)

6. **RESTful API Documentation** (Not Started)
   - Need to add API endpoints with filtering, sorting, and search
   - Need to generate API documentation

## Technical Stack

- **Backend**: Laravel 12.x, PHP 8.3
- **Frontend**: Vue 3, TypeScript, Inertia.js
- **UI Components**: shadcn-vue (Radix Vue based)
- **Styling**: Tailwind CSS
- **Database**: SQLite
- **Build Tools**: Vite
- **Maps**: MapLibre GL

## Current Issues to Fix

1. **E-commerce Implementation**: Need to implement the shopping cart system
2. **Testing**: Comprehensive testing of all features needed

## Features

- **City-based Weather Search**: Search for weather by city name
- **Responsive Design**: Mobile-first approach with optimized layouts for all devices
- **Real-time Data**: Integration with OpenWeatherMap API
- **Data Caching**: Implementation reduces API calls and improves performance
- **Error Handling**: Comprehensive error states for API failures
- **Weather Metrics**: Display of temperature, feels-like, humidity, wind speed, pressure, and visibility
- **Weather Icons**: Dynamic weather icons from OpenWeatherMap
- **Theme Integration**: Styled to match application's existing theme system

## Screenshots

![Weather Dashboard Screenshot](screenshots/weather-dashboard.png)

## Requirements

- PHP 8.1+
- Laravel 10+
- Node.js & NPM
- OpenWeatherMap API key

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/laravel-weather-dashboard.git
   cd laravel-weather-dashboard
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   npm install
   ```

4. Copy environment file and set up your database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Add your OpenWeatherMap API key to the .env file:
   ```
   WEATHER_API_KEY=your_api_key_here
   ```

6. Build assets:
   ```bash
   npm run dev
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

8. Access the application at http://localhost:8000

## Implementation Details

### Backend Controller
```php
private function getWeatherData($city)
{
    try {
        return Cache::remember("weather_{$city}", config('services.open_weather_map.cache_duration', 1800), function () use ($city) {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => config('services.open_weather_map.key'),
                'units' => 'metric',
            ]);
            
            $data = $response->json();
            
            // Error handling...
            
            return $data;
        });
    } catch (\Exception $e) {
        session()->flash('error', 'Connection failed: ' . $e->getMessage());
        return null;
    }
}
```

### Configuration
Add to your `config/services.php`:
```php
'open_weather_map' => [
    'key' => env('WEATHER_API_KEY'),
    'cache_duration' => env('OPENWEATHER_CACHE_DURATION', 1800),
],
```

## Best Practices & References

- **Laravel Documentation**: https://laravel.com/docs/12.x/documentation - Official guide for creating comprehensive API documentation in Laravel

## Deployment Guide (Zone.ee)

This project is deployed on Zone.ee hosting (school-provided account).

### Pre-Deployment Checklist

1. **Prepare your production environment file** (`.env.production`):
   ```env
   APP_NAME="TA23 Hajusrakendused"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.zone.ee
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_zone_database
   DB_USERNAME=your_zone_username
   DB_PASSWORD=your_zone_password
   
   WEATHER_API_KEY=your_openweathermap_api_key
   ```

2. **Optimize Laravel for production**:
   ```bash
   # Clear and cache configurations
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Build production assets
   npm run build
   
   # Optimize composer autoloader
   composer install --optimize-autoloader --no-dev
   ```

### Deployment Methods

#### Option A: Git-based Deployment (Recommended)
1. Set up SSH keys with Zone.ee
2. Create a deployment script:
   ```bash
   #!/bin/bash
   cd /path/to/your/project
   git pull origin main
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

#### Option B: Manual Deployment via SSH/FTP
1. Upload all files except:
   - `node_modules/`
   - `.env` (create separately on server)
   - `storage/app/`
   - `storage/framework/cache/`
   - `storage/framework/sessions/`
   - `storage/logs/`
   
2. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Zone.ee Specific Configuration

1. **Configure document root** to point to `/public` directory
2. **Set up `.htaccess`** (already included in Laravel)
3. **Configure cron job** for Laravel scheduler:
   ```bash
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```

### Security Best Practices

1. **SSL Certificate**: Use Zone.ee's free Let's Encrypt SSL
2. **Environment Security**: Keep `.env` file outside public directory
3. **File Permissions**: 
   - Directories: 755
   - Files: 644
   - Storage/cache: 775
4. **Enable backups** through Zone.ee control panel

### Performance Optimization

1. **Use Redis** for cache and sessions (if available):
   ```env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

2. **Enable OPcache** (usually enabled by default)

3. **Configure queue workers** if using queues:
   ```bash
   php artisan queue:work --daemon
   ```

### Post-Deployment Steps

1. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

2. **Seed production data** (if needed):
   ```bash
   php artisan db:seed --class=ProductionSeeder
   ```

3. **Test all features**:
   - Weather API integration
   - Map functionality
   - Blog system with authentication
   - Emotions/Subjects API
   - E-commerce features

4. **Monitor logs**:
   - Laravel logs: `storage/logs/laravel.log`
   - Server logs through Zone.ee panel

### Maintenance

1. **Regular backups**: Enable automated backups in Zone.ee panel
2. **Monitor resource usage**: Check CPU, memory, and storage usage
3. **Keep dependencies updated**: Regular security updates
4. **Database optimization**: Regular cleanup of old sessions/cache

### Support Resources

- **Zone.ee Documentation**: https://help.zone.ee
- **Laravel Deployment Guide**: https://laravel.com/docs/deployment

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
