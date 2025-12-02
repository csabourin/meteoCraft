# MeteoCraft - Ottawa Weather Display for CraftCMS

A CraftCMS plugin that provides a simple, accessible weather display component for Ottawa, Ontario, using data from Environment and Climate Change Canada (ECCC). Display current weather conditions and today's forecast by time period (morning/afternoon/evening) on your front-end pages using a simple Twig include.

## Features

- **Simple Integration**: Just include a Twig template - no complex setup required
- **Current Weather Conditions**: Real-time temperature, condition, feels-like temperature, humidity, wind speed/direction, and barometric pressure
- **Today's Forecast by Time Period**: Weather breakdown for morning (6am-12pm), afternoon (12pm-6pm), and evening (6pm-12am)
- **Detailed Period Information**: Each time period shows temperature range, conditions, wind, and precipitation probability
- **Bilingual Support**: Full English and French translations, automatically detected from CraftCMS site language
- **WCAG 2.1 AA Compliant**: 100% accessible with proper semantic HTML, ARIA labels, keyboard navigation, and color contrast
- **Customizable Styling**: Includes SCSS file with variables for easy theming
- **Data Caching**: Weather data is cached for 30 minutes to reduce API calls (separate cache per language)
- **PHP in Twig**: Uses Craft 5's PHP support for simple, self-contained logic
- **Official Data Source**: Uses Environment and Climate Change Canada's official weather API with hourly forecasts

## Requirements

- CraftCMS 5.x (for PHP in Twig support / Twig Perversion)
- PHP 8.0 or higher
- cURL extension enabled
- CraftCMS site configured with language settings (for bilingual support)

## Installation

### Quick Install (Recommended)

**Method 1: Manual Installation**
```bash
cd /path/to/your/craftcms/project
mkdir -p plugins
git clone https://github.com/csabourin/meteoCraft.git plugins/meteocraft
./craft plugin/install meteocraft
```

**Method 2: Composer with Path Repository**

Add to your project's `composer.json`:
```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./plugins/meteocraft"
    }
  ]
}
```

Then install:
```bash
composer require csabourin/meteocraft:@dev
./craft plugin/install meteocraft
```

**For detailed installation instructions and troubleshooting, see [INSTALL.md](INSTALL.md)**

## Usage

### Basic Usage

Simply include the weather display template anywhere in your Twig templates:

```twig
{% include 'meteocraft/weather-display' %}
```

That's it! The template will:
- Automatically fetch fresh weather data from Environment Canada
- Detect your site's language (English/French)
- Display current conditions and today's forecast by time period
- Cache the data for 30 minutes to optimize performance

### Styling

Import the SCSS file into your main stylesheet:

```scss
// In your main.scss
@import 'path/to/vendor/csabourin/meteocraft/src/assets/scss/weather-display';
```

Or if using Craft's asset bundling:

```scss
@import '../../../vendor/csabourin/meteocraft/src/assets/scss/weather-display';
```

#### Customizing Colors

You can customize the colors by defining SCSS variables **before** importing the file:

```scss
// Define your custom colors first
$meteocraft-primary-color: #0066cc;
$meteocraft-background: #f8f9fa;
$meteocraft-border-color: #dee2e6;
$meteocraft-text-color: #212529;
$meteocraft-border-radius: 8px;

// Then import the styles
@import 'path/to/vendor/csabourin/meteocraft/src/assets/scss/weather-display';
```

### Advanced Usage

#### Custom City (Future Enhancement)

The template currently displays Ottawa weather. To customize the city in future versions, you can pass a parameter:

```twig
{% include 'meteocraft/weather-display' with {
    city: 'Ottawa'
} %}
```

#### Language Override

The template auto-detects language from your site, but you can override it:

```twig
{% include 'meteocraft/weather-display' with {
    lang: 'fr'
} %}
```

### Example Integration

#### In a Layout Template

```twig
{# templates/_layout.twig #}
<!DOCTYPE html>
<html lang="{{ currentSite.language }}">
<head>
    <title>{{ siteName }}</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <aside class="weather-sidebar">
        {% include 'meteocraft/weather-display' %}
    </aside>

    <main>
        {% block content %}{% endblock %}
    </main>
</body>
</html>
```

#### In a Specific Template

```twig
{# templates/pages/home.twig #}
{% extends "_layout" %}

{% block content %}
    <h1>Welcome</h1>

    <section class="weather-widget">
        <h2>Current Weather</h2>
        {% include 'meteocraft/weather-display' %}
    </section>
{% endblock %}
```

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

### Customizing the Display

The main template is located at:
- **Template**: `src/templates/meteocraft/weather-display.twig`

You can customize:
- Colors and styling via SCSS variables (see Usage > Styling section)
- Layout and information displayed by modifying the template
- Temperature units (currently metric/Celsius)

### Cache Duration

Weather data is cached for 30 minutes by default, with separate caches for English and French. To change this, edit the PHP section in the template file (`src/templates/meteocraft/weather-display.twig`):

```php
// Find this line in the PHP block:
$cache->set($cacheKey, $weatherData, 1800);

// Change 1800 (30 minutes) to your desired duration in seconds
$cache->set($cacheKey, $weatherData, 3600); // Example: 1 hour
```

### Translations

Translation files are located in:
- `src/translations/en/meteocraft.php` (English)
- `src/translations/fr/meteocraft.php` (French)

You can customize any text by editing these files.

## Technical Details

### Architecture

- **Template**: Self-contained Twig template with embedded PHP for weather fetching
- **Styling**: Modular SCSS with customizable variables
- **Dependencies**: Uses native Craft services (cache, translations) and cURL for API calls
- **PHP in Twig**: Leverages Craft 5's Twig Perversion feature for simple, maintainable code

### Data Structure

The template fetches and parses:

- **Current Conditions**: Real-time weather observations from Ottawa Macdonald-Cartier International Airport
- **Hourly Forecasts**: 48 hours of hourly forecasts from the ECCC API
- **Time Period Aggregation**: Groups hourly forecasts into morning (6am-12pm), afternoon (12pm-6pm), and evening (6pm-12am) periods
- **Smart Summarization**: Calculates temperature ranges, most common conditions, and average precipitation for each period

### Error Handling

- Network errors are logged via CraftCMS logging system
- Failed API calls show a user-friendly error message in the template
- Cached data is served if API is temporarily unavailable

## Troubleshooting

### Template shows "Unable to load weather data"

1. Check that your server has internet connectivity
2. Verify cURL is enabled in PHP (`php -m | grep curl`)
3. Ensure Twig Perversion (PHP in Twig) is enabled in your Craft 5 config
4. Check CraftCMS logs at `storage/logs/web.log` for detailed error messages
5. Try clearing the cache: `./craft clear-caches/all`

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

### Version 2.0.0 (2025-12-02)

- **⚠️ BREAKING CHANGE**: Removed Control Panel widget in favor of simple front-end template
- **Simplified Architecture**: Now just a Twig include - no complex field types or widgets
- **PHP in Twig**: Uses Craft 5's PHP support for self-contained, maintainable code
- **SCSS Styling**: Added modular SCSS file with customizable variables
- **Front-end Focused**: Designed for displaying weather on public-facing pages
- **Easier Integration**: Simply include template anywhere in your site

### Version 1.2.0 (2025-12-02) - DEPRECATED

- **Field Type Support**: Added field type that can be used in SuperTable and other field types
- **Entry Integration**: Can now be added to entry pages, not just the dashboard
- **Dual Usage**: Works as both a field type and dashboard widget

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
