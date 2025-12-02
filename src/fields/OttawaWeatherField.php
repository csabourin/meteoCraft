<?php

namespace csabourin\meteocraft\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;

/**
 * Ottawa Weather Field
 *
 * A field type that displays current weather conditions and forecast for Ottawa
 * from Environment and Climate Change Canada (ECCC).
 */
class OttawaWeatherField extends Field
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
    public function getInputHtml($value, ?ElementInterface $element = null): string
    {
        $weatherData = $this->fetchWeatherData();

        // Get current site language
        $language = Craft::$app->language;
        $lang = (strpos($language, 'fr') === 0) ? 'fr' : 'en';

        return Craft::$app->getView()->renderTemplate(
            'meteocraft/fields/input',
            [
                'field' => $this,
                'weatherData' => $weatherData,
                'language' => $lang,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        // Weather data is always fetched fresh, no stored value
        return $this->fetchWeatherData();
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        // Don't store weather data - it's always fetched fresh
        return null;
    }

    /**
     * Fetch weather data from Environment Canada API
     *
     * @return array|null
     */
    private function fetchWeatherData(): ?array
    {
        // Get current site language
        $language = Craft::$app->language;
        $lang = (strpos($language, 'fr') === 0) ? 'fr' : 'en';

        $cacheKey = 'meteocraft_ottawa_weather_' . $lang;
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
                        $weatherData = $this->parseWeatherData($properties, $lang);

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
     * @param string $lang Language code (en or fr)
     * @return array
     */
    private function parseWeatherData(array $properties, string $lang = 'en'): array
    {
        $current = $properties['currentConditions'] ?? null;
        $hourlyForecastGroup = $properties['hourlyForecastGroup'] ?? null;

        $result = [
            'current' => null,
            'periods' => [],
        ];

        // Parse current conditions
        if ($current) {
            $result['current'] = [
                'condition' => $current['condition'][$lang] ?? 'N/A',
                'temperature' => $current['temperature']['value'][$lang] ?? null,
                'feelsLike' => $current['windChill']['value'][$lang] ?? $current['temperature']['value'][$lang] ?? null,
                'humidity' => $current['relativeHumidity']['value'][$lang] ?? null,
                'windSpeed' => $current['wind']['speed']['value'][$lang] ?? null,
                'windDirection' => $current['wind']['direction']['value'][$lang] ?? null,
                'pressure' => $current['pressure']['value'][$lang] ?? null,
                'pressureTrend' => $current['pressure']['tendency'][$lang] ?? null,
                'iconCode' => $current['iconCode']['value'] ?? null,
                'iconUrl' => $current['iconCode']['url'] ?? null,
                'lastUpdated' => $current['timestamp'][$lang] ?? null,
            ];
        }

        // Parse hourly forecasts and group by time of day (today only)
        if ($hourlyForecastGroup && isset($hourlyForecastGroup['hourlyForecasts'])) {
            $hourlyForecasts = $hourlyForecastGroup['hourlyForecasts'];
            $result['periods'] = $this->groupHourlyForecastsByPeriod($hourlyForecasts, $lang);
        }

        return $result;
    }

    /**
     * Group hourly forecasts into morning, afternoon, and evening periods for today
     *
     * @param array $hourlyForecasts
     * @param string $lang Language code (en or fr)
     * @return array
     */
    private function groupHourlyForecastsByPeriod(array $hourlyForecasts, string $lang = 'en'): array
    {
        $periods = [
            'morning' => ['name' => Craft::t('meteocraft', 'Morning'), 'hours' => [], 'range' => Craft::t('meteocraft', 'morning_range')],
            'afternoon' => ['name' => Craft::t('meteocraft', 'Afternoon'), 'hours' => [], 'range' => Craft::t('meteocraft', 'afternoon_range')],
            'evening' => ['name' => Craft::t('meteocraft', 'Evening'), 'hours' => [], 'range' => Craft::t('meteocraft', 'evening_range')],
        ];

        // Get today's date (in UTC to match API timestamps)
        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        $todayDate = $today->format('Y-m-d');

        foreach ($hourlyForecasts as $forecast) {
            $timestamp = $forecast['timestamp'];
            $dt = new \DateTime($timestamp, new \DateTimeZone('UTC'));

            // Convert to Ottawa time (EST/EDT)
            $dt->setTimezone(new \DateTimeZone('America/Toronto'));
            $forecastDate = $dt->format('Y-m-d');
            $hour = (int)$dt->format('H');

            // Only process today's forecasts
            if ($forecastDate !== $todayDate) {
                continue;
            }

            $periodData = [
                'time' => $dt->format('g:i A'),
                'hour' => $hour,
                'temperature' => $forecast['temperature']['value'][$lang] ?? null,
                'feelsLike' => $forecast['windChill']['value'][$lang] ?? $forecast['temperature']['value'][$lang] ?? null,
                'condition' => $forecast['condition'][$lang] ?? 'N/A',
                'iconCode' => $forecast['iconCode']['value'] ?? null,
                'iconUrl' => $forecast['iconCode']['url'] ?? null,
                'windSpeed' => $forecast['wind']['speed']['value'][$lang] ?? null,
                'windDirection' => $forecast['wind']['direction']['value'][$lang] ?? null,
                'precipitation' => $forecast['lop']['value'][$lang] ?? null,
            ];

            // Group by time of day
            if ($hour >= 6 && $hour < 12) {
                $periods['morning']['hours'][] = $periodData;
            } elseif ($hour >= 12 && $hour < 18) {
                $periods['afternoon']['hours'][] = $periodData;
            } elseif ($hour >= 18 && $hour < 24) {
                $periods['evening']['hours'][] = $periodData;
            }
        }

        // Calculate representative data for each period
        $result = [];
        foreach ($periods as $key => $period) {
            if (count($period['hours']) > 0) {
                $result[] = $this->calculatePeriodSummary($period);
            }
        }

        return $result;
    }

    /**
     * Calculate summary statistics for a time period
     *
     * @param array $period
     * @return array
     */
    private function calculatePeriodSummary(array $period): array
    {
        $hours = $period['hours'];
        $count = count($hours);

        if ($count === 0) {
            return null;
        }

        // Get the middle forecast as representative
        $middleIndex = (int)floor($count / 2);
        $representative = $hours[$middleIndex];

        // Calculate temperature range
        $temps = array_column($hours, 'temperature');
        $minTemp = min($temps);
        $maxTemp = max($temps);

        // Get most common condition
        $conditions = array_column($hours, 'condition');
        $conditionCounts = array_count_values($conditions);
        arsort($conditionCounts);
        $mostCommonCondition = key($conditionCounts);

        // Calculate average precipitation chance
        $precips = array_filter(array_column($hours, 'precipitation'));
        $avgPrecip = count($precips) > 0 ? round(array_sum($precips) / count($precips)) : 0;

        return [
            'name' => $period['name'],
            'range' => $period['range'],
            'temperature' => round(($minTemp + $maxTemp) / 2),
            'tempRange' => $minTemp . '°C - ' . $maxTemp . '°C',
            'minTemp' => $minTemp,
            'maxTemp' => $maxTemp,
            'feelsLike' => $representative['feelsLike'],
            'condition' => $mostCommonCondition,
            'iconUrl' => $representative['iconUrl'],
            'iconCode' => $representative['iconCode'],
            'windSpeed' => $representative['windSpeed'],
            'windDirection' => $representative['windDirection'],
            'precipitation' => $avgPrecip,
            'hourlyData' => $hours, // Include all hourly data for tooltip/details
        ];
    }
}
