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

### 🚧 In Progress

4. **Custom API - Emotions** (Partially Complete)
   - Database table: `my_favorite_subject` (follows course requirement)
   - Display page implemented (/display-subjects)
   - Grid layout with colorful cards
   - Create form UI completed with shadcn-vue components
   - TODO: Fix slider component interaction
   - TODO: Add POST route for storing emotions
   - TODO: Implement store method in SubjectController (for MyFavoriteSubject)

5. **E-commerce and Shopping Cart** (Not Started)
   - Product catalog with at least 9 products (image, name, price, description, quantity)
   - Shopping cart functionality (add, update quantity, remove items)
   - Checkout page with user data collection (name, email, phone)
   - Payment integration (Stripe/PayPal)
   - Order management and database storage
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

1. **Slider Component**: The intensity slider in emotions create form is not interactive
2. **Blog Testing**: Need comprehensive testing of all blog features
3. **API Routes**: Missing POST route for emotions (MyFavoriteSubject)
4. **Database Seeders**: 
   - ✓ Posts factory and seeder completed
   - ✓ Emotions creation logic in MyFavoriteSubjectFactory

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

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
