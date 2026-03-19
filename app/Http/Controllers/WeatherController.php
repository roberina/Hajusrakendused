<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WeatherController extends Controller
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweathermap.key');
    }

    public function index()
    {
        return Inertia::render('Weather/Index', [
            'weather'  => session('weather'),
            'forecast' => session('forecast', []),
            'error'    => session('error'),
            'cached'   => session('cached', false),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'city' => 'required|string|min:2|max:100',
        ]);

        $city = trim($request->input('city'));
        $cacheKey = 'weather_' . strtolower(str_replace(' ', '_', $city));

        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($city) {
            $response = Http::timeout(10)->get("{$this->baseUrl}/weather", [
                'q'     => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang'  => 'et',
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if (!$data || (isset($data['cod']) && $data['cod'] != 200)) {
            Cache::forget($cacheKey);
            return redirect()->route('weather.index')->with('error', 'Linna "' . $city . '" ei leitud. Kontrolli kirjaviisi.');
        }

        $forecastKey = 'forecast_' . strtolower(str_replace(' ', '_', $city));

        $forecast = Cache::remember($forecastKey, now()->addMinutes(30), function () use ($city) {
            $response = Http::timeout(10)->get("{$this->baseUrl}/forecast", [
                'q'     => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang'  => 'et',
                'cnt'   => 40,
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        $dailyForecast = [];
        if ($forecast && isset($forecast['list'])) {
            foreach ($forecast['list'] as $item) {
                $date = date('Y-m-d', $item['dt']);
                $hour = (int) date('H', $item['dt']);
                if (!isset($dailyForecast[$date]) || abs($hour - 12) < abs((int) date('H', $dailyForecast[$date]['dt']) - 12)) {
                    $dailyForecast[$date] = $item;
                }
            }
            $dailyForecast = array_values(array_slice($dailyForecast, 0, 5));
        }

        return redirect()->route('weather.index')
            ->with('weather', $data)
            ->with('forecast', $dailyForecast)
            ->with('cached', Cache::has($cacheKey));
    }
}