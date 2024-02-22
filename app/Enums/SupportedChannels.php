<?php

namespace App\Enums;

enum SupportedChannels: string
{
    case MAIL = 'mail';
    case TELEGRAM = 'telegram';
    case CONSOLE = 'console';

    public static function all(): array {
        return [
            self::MAIL->value,
            self::TELEGRAM->value,
            self::CONSOLE->value,
        ];
    }
}
