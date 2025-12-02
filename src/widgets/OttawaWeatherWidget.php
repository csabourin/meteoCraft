<?php

namespace csabourin\meteocraft\widgets;

use Craft;
use craft\base\Widget;

/**
 * Ottawa Weather Widget
 */
class OttawaWeatherWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('meteocraft', 'Ottawa Weather');
    }

    /**
     * @inheritdoc
     */
    public static function icon(): ?string
    {
        return Craft::getAlias('@meteocraft/icon.svg');
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan(): ?int
    {
        return 2;
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml(): ?string
    {
        $weatherData = $this->fetchWeatherData();

        return Craft::$app->getView()->renderTemplate(
            'meteocraft/widgets/ottawa-weather',
            [
                'weatherData' => $weatherData,
            ]
        );
    }

    /**
     * Fetch weather data from Environment Canada API
     *
     * @return array|null
     */
    private function fetchWeatherData(): ?array
    {
        $cacheKey = 'meteocraft_ottawa_weather';
        $cache = Craft::$app->getCache();

        // Try to get cached data (cache for 30 minutes)
        $weatherData = $cache->get($cacheKey);

        if ($weatherData === false) {
            try {
                // Fetch data from ECCC API
                $url = 'https://api.weather.gc.ca/collections/citypageweather-realtime/items?f=json&limit=1&q=Ottawa';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $data = json_decode($response, true);

                    if (isset($data['features'][0]['properties'])) {
                        $properties = $data['features'][0]['properties'];
                        $weatherData = $this->parseWeatherData($properties);

                        // Cache for 30 minutes
                        $cache->set($cacheKey, $weatherData, 1800);
                    }
                }
            } catch (\Exception $e) {
                Craft::error('Error fetching weather data: ' . $e->getMessage(), __METHOD__);
                return null;
            }
        }

        return $weatherData;
    }

    /**
     * Parse the weather data from ECCC API
     *
     * @param array $properties
     * @return array
     */
    private function parseWeatherData(array $properties): array
    {
        $current = $properties['currentConditions'] ?? null;
        $forecastGroup = $properties['forecastGroup'] ?? null;

        $result = [
            'current' => null,
            'forecasts' => [],
        ];

        // Parse current conditions
        if ($current) {
            $result['current'] = [
                'condition' => $current['condition']['en'] ?? 'N/A',
                'temperature' => $current['temperature']['value']['en'] ?? null,
                'feelsLike' => $current['windChill']['value']['en'] ?? $current['temperature']['value']['en'] ?? null,
                'humidity' => $current['relativeHumidity']['value']['en'] ?? null,
                'windSpeed' => $current['wind']['speed']['value']['en'] ?? null,
                'windDirection' => $current['wind']['direction']['value']['en'] ?? null,
                'pressure' => $current['pressure']['value']['en'] ?? null,
                'pressureTrend' => $current['pressure']['tendency']['en'] ?? null,
                'iconCode' => $current['iconCode']['value'] ?? null,
                'iconUrl' => $current['iconCode']['url'] ?? null,
                'lastUpdated' => $current['timestamp']['en'] ?? null,
            ];
        }

        // Parse forecasts (get next 3 days - skip tonight if present)
        if ($forecastGroup && isset($forecastGroup['forecasts'])) {
            $forecasts = $forecastGroup['forecasts'];
            $daysAdded = 0;
            $dayForecasts = [];

            foreach ($forecasts as $forecast) {
                $periodName = $forecast['period']['textForecastName']['en'] ?? '';

                // Skip night forecasts, only get day forecasts
                if (strpos(strtolower($periodName), 'night') !== false) {
                    continue;
                }

                $dayForecasts[] = [
                    'period' => $periodName,
                    'day' => $forecast['period']['value']['en'] ?? '',
                    'temperature' => $forecast['temperatures']['temperature'][0]['value']['en'] ?? null,
                    'tempClass' => $forecast['temperatures']['temperature'][0]['class']['en'] ?? 'high',
                    'condition' => $forecast['cloudPrecip']['en'] ?? 'N/A',
                    'summary' => $forecast['textSummary']['en'] ?? '',
                    'iconCode' => $forecast['abbreviatedForecast']['icon']['value'] ?? null,
                    'iconUrl' => $forecast['abbreviatedForecast']['icon']['url'] ?? null,
                    'iconSummary' => $forecast['abbreviatedForecast']['textSummary']['en'] ?? '',
                    'humidity' => $forecast['relativeHumidity']['value']['en'] ?? null,
                    'windSummary' => $forecast['winds']['textSummary']['en'] ?? null,
                ];

                $daysAdded++;

                // Get next 3 days only
                if ($daysAdded >= 3) {
                    break;
                }
            }

            $result['forecasts'] = $dayForecasts;
        }

        return $result;
    }
}
