<?php

namespace App\Console\Commands;

use App\Enums\SupportedChannels;
use App\Enums\SupportedProviders;
use App\Services\AccuWeatherService;
use App\Services\OpenWeatherService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

class GetWeather extends Command implements PromptsForMissingInput
{
    private $accuWeatherService;
    private $openWeatherService;

    public function __construct(AccuWeatherService $accuWeatherService, OpenWeatherService $openWeatherService)
    {
        parent::__construct();
        $this->accuWeatherService = $accuWeatherService;
        $this->openWeatherService = $openWeatherService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather
                            {provider : Weather provider (open-weather-map|accu-weather)}
                            {city : The city you want to know weather for}
                            {channel? : Output channel (mail|telegram|console)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the current weather';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = $this->argument('provider');
        $city = $this->argument('city');
        $channel = $this->argument('channel') ?? SupportedChannels::CONSOLE->value;

        // validate provider argument
        if (!in_array($provider, SupportedProviders::all())) {
            $this->error("Unsupported provider");
            return;
        }

        // validate channel argument
        if (!in_array($channel, SupportedChannels::all())) {
            $this->error("Unsupported channel");
            return;
        }

        $destination = ''; // we need additional destination value for telegram and mail channels
        if ($channel == SupportedChannels::MAIL->value) {
            $destination = $this->ask("Enter the email you want to send results to");
        } elseif ($channel == SupportedChannels::TELEGRAM->value) {
            $destination = $this->ask("Enter Telegram User ID");
        }

        if ($provider == SupportedProviders::ACCU_WEATHER->value) {
            try {
                $currentWeather = $this->accuWeatherService->getWeatherData($city);
            } catch (Exception $e) {
                $this->error($e->getMessage());
                return;
            }
        } else { // because for now we only have 2 providers
            try {
                $currentWeather = $this->openWeatherService->getWeatherData($city);
            } catch (Exception $e) {
                $this->error($e->getMessage());
                return;
            }
        }

        $this->info("The weather in " . Str::title($city) . " right now is " . $currentWeather ." °C");
    }
}
