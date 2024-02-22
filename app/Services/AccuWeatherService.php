<?php

namespace App\Services;

use App\Interfaces\WeatherProvider;
use Exception;
use Illuminate\Support\Facades\Http;

class AccuWeatherService implements WeatherProvider
{
    private string $url;
    private string $locationsUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->url = config('app.accu_weather_url');
        $this->locationsUrl = config('app.accu_weather_locations_url');
        $this->apiKey = config('app.accu_weather_key');
    }

    /**
     * @throws Exception
     */
    public function getWeatherData(string $city): string
    {
        $locationKey = $this->getLocationKey($city);
        $data = $this->sendRequest([
            'locationKey' => $locationKey,
            'apikey' => $this->apiKey,
        ]);

        // check if city is found and request is successful
        if (empty($data)) {
            throw new Exception("City not found");
        }

        return $data[0]['Temperature']['Metric']['Value'];
    }

    public function sendRequest(mixed $params): array
    {
        return Http::get($this->url . '/' . $params['locationKey'], [
            'apikey' => $params['apikey'],
        ])->json();
    }

    /**
     * @throws Exception
     */
    private function getLocationKey(string $city): string
    {
        $response = Http::get($this->locationsUrl, [
            'q' => $city,
            'apikey' => $this->apiKey,
        ])->json();

        if (empty($response)) {
            throw new Exception("City not found");
        }

        return $response[0]['Key'];
    }
}
