# Installation Instructions

## Method 1: Manual Installation (Recommended for Development)

1. Clone or download this repository into your CraftCMS plugins directory:

```bash
cd /path/to/your/craftcms/project
mkdir -p plugins
git clone https://github.com/csabourin/meteoCraft.git plugins/meteocraft
```

2. Install via CraftCMS Control Panel:
   - Go to **Settings** → **Plugins**
   - Find "MeteoCraft" in the list
   - Click **Install**

Or via command line:

```bash
./craft plugin/install meteocraft
```

## Method 2: Composer with Path Repository

If you want to use Composer, add this to your project's `composer.json`:

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

Then require the package:

```bash
composer require csabourin/meteocraft:@dev
```

## Method 3: Composer with Git Repository

Add this to your project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/csabourin/meteoCraft.git"
    }
  ]
}
```

Then install:

```bash
composer require csabourin/meteocraft:^1.1
```

## Method 4: Private Packagist (For Production)

For production environments, we recommend using [Private Packagist](https://packagist.com/) or [Satis](https://github.com/composer/satis) to host your private packages.

## Troubleshooting Composer Installation

### "Could not find a version matching your minimum-stability"

This error occurs when installing without proper version constraints. Use one of these solutions:

**Solution 1:** Specify version explicitly with dev stability
```bash
composer require csabourin/meteocraft:dev-main
# or
composer require csabourin/meteocraft:@dev
```

**Solution 2:** Lower minimum-stability in your project's composer.json
```json
{
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

**Solution 3:** Use the path repository method (recommended for local development)

### Git Tag Required

If you're installing from a Git repository, make sure the repository has tags:

```bash
git tag v1.1.0
git push origin v1.1.0
```

## Verifying Installation

After installation, verify the plugin is loaded:

```bash
./craft plugin/list
```

You should see "MeteoCraft" in the list of installed plugins.

## Post-Installation

1. Navigate to your CraftCMS Dashboard
2. Click the **New Widget** button
3. Select **Ottawa Weather** from the widget types
4. The widget will immediately start displaying weather data

## Requirements

- CraftCMS 3.x, 4.x, or 5.x
- PHP 7.4 or higher
- cURL extension enabled
- JSON extension enabled
- Internet connection to fetch weather data from ECCC API
