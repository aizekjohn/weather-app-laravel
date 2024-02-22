<?php

namespace App\Interfaces;

interface WeatherProvider
{
    public function getWeatherData(string $city): string;
    public function sendRequest(mixed $params): array;
}
