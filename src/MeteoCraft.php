<?php

namespace csabourin\meteocraft;

use Craft;
use craft\base\Plugin;
use csabourin\meteocraft\fields\OttawaWeatherField;
use csabourin\meteocraft\widgets\OttawaWeatherWidget;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\web\View;
use yii\base\Event;

/**
 * MeteoCraft Plugin
 *
 * @property \craft\services\Dashboard $dashboard
 * @property \craft\services\Fields $fields
 */
class MeteoCraft extends Plugin
{
    /**
     * @var MeteoCraft
     */
    public static $plugin;

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register the @meteocraft alias
        Craft::setAlias('@meteocraft', __DIR__);

        // Register the dashboard widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OttawaWeatherWidget::class;
            }
        );

        // Register the field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OttawaWeatherField::class;
            }
        );

        // Expose this plugin's templates under the `meteocraft` namespace
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots['meteocraft'] = __DIR__ . '/templates';
            }
        );

        Craft::info(
            'MeteoCraft plugin loaded',
            __METHOD__
        );
    }
}
