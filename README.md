# Ottawa Weather Display Component

Simple, accessible weather display component for Ottawa, Ontario, using data from Environment and Climate Change Canada (ECCC). Built for Craft CMS 5 with PHP in Twig support.

## Features

- **Self-Contained**: Single Twig template with embedded PHP - no plugin required
- **Current Weather**: Real-time temperature, humidity, wind, pressure, and conditions
- **Today's Forecast**: Weather breakdown by time period (morning, afternoon, evening)
- **Bilingual**: Full English and French support with translations
- **Accessible**: WCAG 2.1 AA compliant with semantic HTML and ARIA labels
- **Customizable**: SCSS file with variables for easy theming
- **Cached**: 30-minute cache to optimize API calls
- **Official Data**: Environment and Climate Change Canada API

## Requirements

- Craft CMS 5.x
- PHP 8.0 or higher
- PHP in Twig (Twig Perversion) enabled
- cURL extension

## Installation

### 1. Copy Files to Your Craft Project

Copy the files from this repository to your Craft installation:

```bash
# From this repository root, copy to your Craft project:
cp -r templates/_weather /path/to/your/craft/templates/
cp -r assets/scss /path/to/your/craft/assets/  # or wherever you keep SCSS
cp -r translations /path/to/your/craft/modules/yourmodule/  # or your module directory
```

### 2. Register Translations in Your Module

Add the translation category to your module's `config/app.php`:

```php
return [
    'modules' => [
        'yourmodule' => \modules\yourmodule\Module::class,
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'weather' => [
                    'class' => craft\i18n\PhpMessageSource::class,
                    'sourceLanguage' => 'en',
                    'basePath' => '@modules/yourmodule/translations',
                    'allowOverrides' => true,
                ],
            ],
        ],
    ],
];
```

Or if you prefer, add it directly in your module's `init()` method:

```php
public function init()
{
    parent::init();

    Craft::$app->i18n->translations['weather'] = [
        'class' => PhpMessageSource::class,
        'sourceLanguage' => 'en',
        'basePath' => __DIR__ . '/translations',
        'allowOverrides' => true,
    ];
}
```

### 3. Import SCSS

Add to your main stylesheet:

```scss
// In your main.scss
@import 'path/to/assets/scss/weather';
```

#### Customize Colors

Define variables **before** importing:

```scss
// Custom colors
$meteocraft-primary-color: #0066cc;
$meteocraft-background: #f8f9fa;
$meteocraft-border-color: #dee2e6;
$meteocraft-text-color: #212529;
$meteocraft-border-radius: 8px;

// Import styles
@import 'path/to/assets/scss/weather';
```

## Usage

### Basic Usage

Include the template anywhere in your Twig files:

```twig
{% include '_weather/display' %}
```

### With Options

```twig
{% include '_weather/display' with {
    city: 'Ottawa',
    lang: 'fr'
} %}
```

### Example Integrations

#### In a Layout

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
        {% include '_weather/display' %}
    </aside>

    <main>
        {% block content %}{% endblock %}
    </main>
</body>
</html>
```

#### In a Page Template

```twig
{# templates/index.twig #}
{% extends "_layout" %}

{% block content %}
    <h1>Welcome</h1>

    <section class="weather-widget">
        <h2>Current Weather in Ottawa</h2>
        {% include '_weather/display' %}
    </section>
{% endblock %}
```

## File Structure

```
/your-craft-project/
├── templates/
│   └── _weather/
│       └── display.twig          # Main weather display template
├── assets/
│   └── scss/
│       └── _weather.scss          # Styles with variables
└── modules/
    └── yourmodule/
        └── translations/
            ├── en/
            │   └── weather.php    # English translations
            └── fr/
                └── weather.php    # French translations
```

## Configuration

### Cache Duration

Edit the PHP block in `templates/_weather/display.twig`:

```php
// Find this line (around line 145):
$cache->set($cacheKey, $weatherData, 1800);

// Change 1800 (30 minutes) to your desired duration in seconds:
$cache->set($cacheKey, $weatherData, 3600); // 1 hour
```

### Customizing Display

The template is fully editable. Modify `templates/_weather/display.twig` to:
- Change layout and styling
- Add or remove weather data points
- Adjust time periods (currently: morning 6am-12pm, afternoon 12pm-6pm, evening 6pm-12am)

### Translations

Edit the translation files to customize any text:
- `translations/en/weather.php` - English
- `translations/fr/weather.php` - French

## Data Source

- **API**: Environment and Climate Change Canada (ECCC)
- **Endpoint**: `https://api.weather.gc.ca/collections/citypageweather-realtime`
- **Documentation**: [ECCC Open Data](https://eccc-msc.github.io/open-data/msc-data/citypage-weather/readme_citypageweather_en/)
- **Location**: Ottawa (Kanata - Orléans)

## Accessibility

This component is **WCAG 2.1 Level AA compliant**:

- ✓ Proper semantic HTML5 elements
- ✓ ARIA labels and roles
- ✓ Keyboard accessible
- ✓ Screen reader friendly
- ✓ High contrast ratios (4.5:1+)
- ✓ Bilingual support

See [ACCESSIBILITY.md](ACCESSIBILITY.md) for details.

## Troubleshooting

### Template shows "Unable to load weather data"

1. Verify server has internet connectivity
2. Check cURL is enabled: `php -m | grep curl`
3. Ensure PHP in Twig is enabled in your Craft config
4. Check logs: `storage/logs/web.log`
5. Clear cache: `./craft clear-caches/all`

### Translations not working

1. Verify translations are registered in `config/app.php` or your module
2. Check file paths match your module structure
3. Clear cache: `./craft clear-caches/all`
4. Verify translation category is 'weather' (not 'meteocraft')

### Styling not applied

1. Verify SCSS file is imported in your main stylesheet
2. Check file path is correct
3. Rebuild your CSS: `npm run build` or your build command
4. Clear browser cache

## License

MIT License - see [LICENSE](LICENSE) file

## Credits

- Weather data: [Environment and Climate Change Canada](https://weather.gc.ca/)
- Component by: csabourin

## Support

For issues or questions: [GitHub Repository](https://github.com/csabourin/meteoCraft)

---

## Alternative: Using with Your Existing Controller

If you prefer to use your existing controller's `actionFetchJson()` method instead of the embedded PHP, you can modify the template to make an AJAX call:

```twig
{# In templates/_weather/display.twig, replace the {% php %} block with: #}
<div class="meteocraft-weather-display" data-url="{{ url('external-api-data/data/fetch-json', {
    url: 'https://api.weather.gc.ca/collections/citypageweather-realtime/items?f=json&limit=1&q=Ottawa'|url_encode
}) }}">
    <div class="loading">Loading weather data...</div>
</div>

{# Add JavaScript to fetch and render #}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.meteocraft-weather-display');
    const url = container.dataset.url;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Parse and render weather data
            // ... your rendering logic
        });
});
</script>
```

However, note that your current controller only allows `services2.arcgis.com` in the allowlist, so you'd need to add `api.weather.gc.ca` to use it for weather data.
