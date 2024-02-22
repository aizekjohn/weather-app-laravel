<?php

namespace App\Services;

use App\Interfaces\WeatherProvider;
use Exception;
use Illuminate\Support\Facades\Http;

class OpenWeatherService implements WeatherProvider
{
    private string $url;
    private string $apiKey;

    public function __construct()
    {
        $this->url = config('app.open_weather_map_url');
        $this->apiKey = config('app.open_weather_map_key');
    }

    /**
     * @throws Exception
     */
    public function getWeatherData(string $city): string
    {
        $data = $this->sendRequest([
            'q' => $city,
            'units' => 'metric',
            'appid' => $this->apiKey,
        ]);

        // check if city is found and request is successful
        if (!array_key_exists('main', $data)) {
            throw new Exception("City not found");
        }

        return $data['main']['temp'];
    }

    public function sendRequest(mixed $params): array
    {
        return Http::get($this->url, $params)->json();
    }
}
