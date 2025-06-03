<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $city = $request->input('city', 'Kuressaare');
        $weatherData = $this->getWeatherData($city);
        $markers = Marker::all();

        return Inertia::render('Dashboard', [
            'weatherData' => $weatherData,
            'error' => session('error'),
            'markers' => $markers,
        ]);
    }

    /**
     * Get weather data for a specific city
     */
    private function getWeatherData($city)
    {
        try {
            return Cache::remember("weather_{$city}", config('services.open_weather_map.cache_duration', 1800), function () use ($city) {
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => config('services.open_weather_map.key'),
                    'units' => 'metric',
                ]);

                $data = $response->json();

                if ($data['cod'] == '404') {
                    session()->flash('error', "{$city} not found");

                    return null;
                } elseif ($data['cod'] == '401') {
                    session()->flash('error', 'Invalid API key');

                    return null;
                } elseif ($data['cod'] == '429') {
                    session()->flash('error', 'Too many requests. Please try again later.');

                    return null;
                } elseif (in_array($data['cod'], ['500', '502', '503', '504'])) {
                    session()->flash('error', 'Server error. Please try again later');

                    return null;
                }

                return $data;
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Connection failed: '.$e->getMessage());

            return null;
        }
    }
}
