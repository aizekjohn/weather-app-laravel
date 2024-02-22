<?php

namespace App\Enums;

enum SupportedProviders: string
{
    case OPEN_WEATHER_MAP = 'open-weather-map';
    case ACCU_WEATHER = 'accu-weather';

    public static function all(): array {
        return [
            self::OPEN_WEATHER_MAP->value,
            self::ACCU_WEATHER->value,
        ];
    }
}
