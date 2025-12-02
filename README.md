# MeteoCraft - Ottawa Weather Widget for CraftCMS

A CraftCMS dashboard widget that displays current weather conditions and today's forecast by time period (morning/afternoon/evening) for Ottawa, Ontario, using data from Environment and Climate Change Canada (ECCC).

## Features

- **Current Weather Conditions**: Real-time temperature, condition, feels-like temperature, humidity, wind speed/direction, and barometric pressure
- **Today's Forecast by Time Period**: Weather breakdown for morning (6am-12pm), afternoon (12pm-6pm), and evening (6pm-12am)
- **Detailed Period Information**: Each time period shows temperature range, conditions, feels-like temperature, wind, and precipitation probability
- **Bilingual Support**: Full English and French translations, automatically detected from CraftCMS site language
- **WCAG 2.1 AA Compliant**: 100% accessible with proper semantic HTML, ARIA labels, keyboard navigation, and color contrast
- **Beautiful Design**: Modern, responsive widget design with gradient styling
- **Data Caching**: Weather data is cached for 30 minutes to reduce API calls (separate cache per language)
- **Official Data Source**: Uses Environment and Climate Change Canada's official weather API with hourly forecasts

## Requirements

- CraftCMS 3.x, 4.x, or 5.x
- PHP 7.4 or higher
- cURL extension enabled
- CraftCMS site configured with language settings (for bilingual support)

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
- Today's forecast broken down by time periods (morning, afternoon, evening) in a grid layout
- Detailed information for each period including temperature range, conditions, wind, and precipitation
- All text in the language configured for your CraftCMS site (English or French)
- Weather data updates automatically every 30 minutes

## Data Source

This plugin uses the official Environment and Climate Change Canada (ECCC) weather API:

- **API Endpoint**: `https://api.weather.gc.ca/collections/citypageweather-realtime`
- **Documentation**: [ECCC Open Data - City Page Weather](https://eccc-msc.github.io/open-data/msc-data/citypage-weather/readme_citypageweather_en/)
- **Location**: Ottawa (Kanata - Orléans) - Site Code: on-118

## Accessibility

This widget is **100% WCAG 2.1 Level AA compliant**. Features include:

- **Color Contrast**: All text meets or exceeds 4.5:1 contrast ratio for normal text
- **Keyboard Navigation**: Fully keyboard accessible with visible focus indicators
- **Screen Reader Support**: Proper ARIA labels, semantic HTML, and screen reader announcements
- **Bilingual**: Full support for English and French, including ARIA labels
- **Semantic HTML**: Uses proper HTML5 elements for structure
- **No Motion Sickness**: Subtle, non-disruptive animations

See [ACCESSIBILITY.md](ACCESSIBILITY.md) for detailed compliance information.

## Bilingual Support

The widget automatically detects your CraftCMS site's language setting:

- **English**: Full English translations for all text and ARIA labels
- **French**: Full French translations (Français) for Quebec/Canadian French
- **API Data**: Weather data from ECCC API is fetched in the appropriate language
- **Cache**: Separate caches maintained for each language

### Supported Languages

- English (en)
- French (fr, fr-CA)

The language is automatically detected from `Craft::$app->language`. If your site language starts with "fr", French translations are used; otherwise, English is the default.

## Configuration

### Customizing the Widget

The widget template is located at `src/templates/widgets/ottawa-weather.twig`. You can customize:

- Colors and styling in the `<style>` section
- Layout and information displayed
- Temperature units (currently metric/Celsius)

### Cache Duration

Weather data is cached for 30 minutes by default, with separate caches for English and French. To change this, edit the cache duration in `src/widgets/OttawaWeatherWidget.php`:

```php
// Change 1800 (30 minutes) to your desired duration in seconds
$cache->set($cacheKey, $weatherData, 1800);
```

### Translations

Translation files are located in:
- `src/translations/en/meteocraft.php` (English)
- `src/translations/fr/meteocraft.php` (French)

You can customize any text by editing these files.

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
- **Hourly Forecasts**: 48 hours of hourly forecasts from the ECCC API
- **Time Period Aggregation**: Groups hourly forecasts into morning (6am-12pm), afternoon (12pm-6pm), and evening (6pm-12am) periods
- **Smart Summarization**: Calculates temperature ranges, most common conditions, and average precipitation for each period

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

### Version 1.1.0 (2025-12-02)

- **Bilingual Support**: Full English and French translations
- **WCAG 2.1 AA Compliance**: 100% accessible with proper ARIA labels, semantic HTML, and keyboard navigation
- **Improved Color Contrast**: Enhanced colors to meet AA standards (5.5:1 to 16:1 ratios)
- **Semantic HTML**: Proper use of sections, articles, and definition lists
- **Screen Reader Support**: Comprehensive ARIA labels and roles
- **Separate Language Caches**: Independent caching for English and French data

### Version 1.0.1 (2025-12-02)

- Updated to show today's weather by time period (morning/afternoon/evening)
- Enhanced period cards with temperature ranges and detailed information
- Added precipitation probability display
- Improved data aggregation from hourly forecasts

### Version 1.0.0 (2025-12-02)

- Initial release
- Current weather conditions display
- Weather forecast display
- Data caching (30 minutes)
- Responsive widget design
