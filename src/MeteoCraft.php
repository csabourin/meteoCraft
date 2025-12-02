<?php

namespace csabourin\meteocraft;

use Craft;
use craft\base\Plugin;
use csabourin\meteocraft\widgets\OttawaWeatherWidget;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use yii\base\Event;

/**
 * MeteoCraft Plugin
 *
 * @property \craft\services\Dashboard $dashboard
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

        // Register the widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OttawaWeatherWidget::class;
            }
        );

        Craft::info(
            'MeteoCraft plugin loaded',
            __METHOD__
        );
    }
}
