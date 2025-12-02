# MeteoCraft - Ottawa Weather Widget for CraftCMS

A CraftCMS dashboard widget that displays current weather conditions and a 3-day forecast for Ottawa, Ontario, using data from Environment and Climate Change Canada (ECCC).

## Features

- **Current Weather Conditions**: Temperature, condition, feels-like temperature, humidity, wind speed/direction, and barometric pressure
- **3-Day Forecast**: Upcoming weather forecasts with temperatures and conditions
- **Beautiful Design**: Modern, responsive widget design with gradient styling
- **Data Caching**: Weather data is cached for 30 minutes to reduce API calls
- **Official Data Source**: Uses Environment and Climate Change Canada's official weather API

## Requirements

- CraftCMS 3.x, 4.x, or 5.x
- PHP 7.4 or higher
- cURL extension enabled

## Installation

### Via Composer (Recommended)

1. Add the plugin to your project:

```bash
composer require csabourin/meteocraft
```

2. Install the plugin in the CraftCMS Control Panel:
   - Go to **Settings** → **Plugins**
   - Find "MeteoCraft" in the list
   - Click **Install**

### Manual Installation

1. Download or clone this repository into your `plugins` directory:

```bash
cd /path/to/craft/plugins
git clone https://github.com/csabourin/meteoCraft.git meteocraft
```

2. Install via CraftCMS Control Panel as described above, or via command line:

```bash
./craft plugin/install meteocraft
```

## Usage

1. Navigate to your CraftCMS Dashboard
2. Click the **New Widget** button
3. Select **Ottawa Weather** from the widget types
4. Click **Save**

The widget will display:
- Current weather conditions at the top with a purple gradient background
- Three-day forecast below in a grid layout
- Weather data updates automatically every 30 minutes

## Data Source

This plugin uses the official Environment and Climate Change Canada (ECCC) weather API:

- **API Endpoint**: `https://api.weather.gc.ca/collections/citypageweather-realtime`
- **Documentation**: [ECCC Open Data - City Page Weather](https://eccc-msc.github.io/open-data/msc-data/citypage-weather/readme_citypageweather_en/)
- **Location**: Ottawa (Kanata - Orléans) - Site Code: on-118

## Configuration

### Customizing the Widget

The widget template is located at `src/templates/widgets/ottawa-weather.twig`. You can customize:

- Colors and styling in the `<style>` section
- Layout and information displayed
- Temperature units (currently metric/Celsius)

### Cache Duration

Weather data is cached for 30 minutes by default. To change this, edit the cache duration in `src/widgets/OttawaWeatherWidget.php`:

```php
// Change 1800 (30 minutes) to your desired duration in seconds
$cache->set($cacheKey, $weatherData, 1800);
```

### Adding a Custom Icon

To add a custom icon for the widget:

1. Create an SVG icon file at `src/icon.svg`
2. The widget will automatically use it in the dashboard

## Technical Details

### Widget Class

- **Class**: `csabourin\meteocraft\widgets\OttawaWeatherWidget`
- **Extends**: `craft\base\Widget`
- **Max Colspan**: 2 (can span 2 columns in the dashboard)

### Data Structure

The widget fetches and parses:

- **Current Conditions**: Real-time weather observations from Ottawa Macdonald-Cartier International Airport
- **Forecasts**: Day and night forecasts with temperatures, conditions, wind, humidity, and weather icons

### Error Handling

- Network errors are logged via CraftCMS logging system
- Failed API calls show a user-friendly error message
- Cached data is served if API is temporarily unavailable

## Troubleshooting

### Widget shows "Unable to load weather data"

1. Check that your server has internet connectivity
2. Verify cURL is enabled in PHP (`php -m | grep curl`)
3. Check CraftCMS logs at `storage/logs/web.log` for detailed error messages
4. Try clearing the cache: `./craft clear-caches/all`

### Weather data is not updating

Weather data is cached for 30 minutes. To force a refresh:

```bash
./craft clear-caches/data-caches
```

Or clear the cache via Control Panel: **Utilities** → **Clear Caches** → **Data caches**

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- Weather data provided by [Environment and Climate Change Canada](https://weather.gc.ca/)
- Plugin developed by csabourin

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/csabourin/meteoCraft).

## Changelog

### Version 1.0.0 (2025-12-02)

- Initial release
- Current weather conditions display
- 3-day forecast
- Data caching (30 minutes)
- Responsive widget design
