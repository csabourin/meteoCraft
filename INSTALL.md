# Installation Guide

This guide will help you integrate the Ottawa Weather Display component into your Craft CMS project.

## Prerequisites

Before installing, ensure you have:

- ✅ Craft CMS 5.x installed and running
- ✅ PHP 8.0 or higher
- ✅ PHP cURL extension enabled
- ✅ PHP in Twig (Twig Perversion) enabled in your Craft config
- ✅ A Craft module set up (for translations)

## Quick Installation

### Step 1: Copy Files

From this repository, copy the files to your Craft project:

```bash
# Navigate to this repository
cd /path/to/meteoCraft

# Copy template
cp -r templates/_weather /path/to/your/craft/templates/

# Copy SCSS (adjust path to match your project structure)
cp -r assets/scss /path/to/your/craft/assets/

# Copy translations to your module
cp -r translations /path/to/your/craft/modules/yourmodule/
```

### Step 2: Register Translations

#### Option A: In `config/app.php`

Add the translation category to your `config/app.php`:

```php
<?php
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

#### Option B: In Your Module

Alternatively, register translations in your module's `init()` method:

```php
<?php
namespace modules\yourmodule;

use Craft;
use craft\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        // Register weather translations
        Craft::$app->i18n->translations['weather'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/translations',
            'allowOverrides' => true,
        ];
    }
}
```

### Step 3: Import SCSS

Add the weather styles to your main stylesheet:

```scss
// In your main.scss or wherever you keep your imports
@import '../assets/scss/weather';
```

Or with custom variables:

```scss
// Define custom colors BEFORE importing
$meteocraft-primary-color: #0066cc;
$meteocraft-background: #f8f9fa;
$meteocraft-border-color: #dee2e6;
$meteocraft-text-color: #212529;
$meteocraft-border-radius: 8px;

// Then import
@import '../assets/scss/weather';
```

### Step 4: Use the Template

Include the weather display in any Twig template:

```twig
{% include '_weather/display' %}
```

## Verification

### Test the Installation

1. **Create a test page:**

   ```twig
   {# templates/test-weather.twig #}
   {% extends "_layout" %}

   {% block content %}
       <h1>Weather Test</h1>
       {% include '_weather/display' %}
   {% endblock %}
   ```

2. **Visit the page** in your browser

3. **Check for weather data:**
   - You should see current conditions
   - You should see forecast periods (morning, afternoon, evening)
   - Text should be in your site's language (English or French)

### Troubleshooting

#### "Unable to load weather data"

**Check PHP in Twig is enabled:**

In `config/general.php`:

```php
return [
    '*' => [
        'phpSessionName' => 'CraftSessionId',
        // ... other settings
    ],
    'dev' => [
        'allowAdminChanges' => true,
        'devMode' => true,
        'enableTemplateCaching' => false,
    ],
];
```

Then verify in `config/app.php` or your environment config that Twig Perversion is enabled.

**Check cURL:**

```bash
php -m | grep curl
```

If cURL is not listed, install it:

```bash
# Ubuntu/Debian
sudo apt-get install php-curl

# macOS (using Homebrew)
brew install php
```

**Check logs:**

```bash
tail -f storage/logs/web.log
```

Look for errors related to weather data fetching.

**Clear cache:**

```bash
./craft clear-caches/all
```

#### Translations Not Working

**Verify translation category:**

The template uses `Craft::t('weather', ...)`. Make sure:

1. Translation files are named `weather.php` (not `meteocraft.php`)
2. Translation category in app.php is `'weather'`
3. File paths are correct

**Clear cache:**

```bash
./craft clear-caches/all
```

**Test translation:**

Create a test template:

```twig
{{ 'Morning'|t('weather') }}
```

Should output "Morning" in English or "Matin" in French.

#### Styles Not Applied

**Check SCSS import path:**

Verify the path in your import statement matches your file structure.

**Rebuild CSS:**

```bash
npm run build
# or
npm run dev
# or whatever your build command is
```

**Check browser console:**

Look for 404 errors on CSS files.

**Clear cache:**

- Browser cache: Hard refresh (Cmd+Shift+R or Ctrl+Shift+R)
- Craft cache: `./craft clear-caches/all`

## File Structure After Installation

Your Craft project should look like this:

```
your-craft-project/
├── config/
│   └── app.php                          # Translation registration
├── modules/
│   └── yourmodule/
│       └── translations/
│           ├── en/
│           │   └── weather.php          # English translations
│           └── fr/
│               └── weather.php          # French translations
├── templates/
│   └── _weather/
│       └── display.twig                 # Weather display template
└── assets/                              # or src/, or wherever you keep assets
    └── scss/
        └── _weather.scss                # Weather styles
```

## Configuration Options

### Cache Duration

Default: 30 minutes (1800 seconds)

To change, edit `templates/_weather/display.twig`:

```php
// Around line 145, change:
$cache->set($cacheKey, $weatherData, 1800);

// To your preferred duration:
$cache->set($cacheKey, $weatherData, 3600); // 1 hour
$cache->set($cacheKey, $weatherData, 900);  // 15 minutes
```

### Language

Auto-detected from `Craft::$app->language`

To override:

```twig
{% include '_weather/display' with {
    lang: 'fr'
} %}
```

### City

Currently hardcoded to Ottawa.

To change (future enhancement):

```twig
{% include '_weather/display' with {
    city: 'Toronto'
} %}
```

Note: The API endpoint will need to support the city you specify.

## Advanced: Customization

### Modify Template Layout

Edit `templates/_weather/display.twig` to:
- Change which data points are displayed
- Adjust time period definitions
- Modify HTML structure
- Add custom styling classes

### Add Custom Translations

Edit `translations/en/weather.php` or `translations/fr/weather.php`:

```php
return [
    'Morning' => 'Morning',
    'Custom Text' => 'Your custom translation',
    // ... add more
];
```

Use in template:

```twig
{{ 'Custom Text'|t('weather') }}
```

### Theme Integration

The SCSS file uses CSS variables you can override:

```scss
// In your theme's variables file
$meteocraft-primary-color: var(--your-theme-primary);
$meteocraft-background: var(--your-theme-bg);

// Then import weather styles
@import 'path/to/weather';
```

## Support

If you encounter issues:

1. Check the [README.md](README.md) for general usage
2. Check the [Troubleshooting section](#troubleshooting) above
3. Enable Dev Mode and check `storage/logs/web.log`
4. Open an issue on [GitHub](https://github.com/csabourin/meteoCraft)

## Next Steps

- Customize the styling to match your site's theme
- Add the weather display to your desired templates
- Consider caching duration based on your needs
- Customize translations if needed
