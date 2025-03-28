# Laravel Weather Dashboard

A responsive weather dashboard component for Laravel applications using OpenWeatherMap API.

## Project Overview

This project implements a weather dashboard in multiple development phases:

1. **Initial Development**: Built a standalone Laravel application with weather search and display functionality
   
2. **Framework Migration**: Refactored the application to use Vue.js and Inertia.js for improved interactivity
   
3. **Component Integration**: Transformed the weather application into a reusable card component
   
4. **Project Incorporation**: Successfully integrated the weather card into a dashboard project

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
