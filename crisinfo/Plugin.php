<?php namespace Bree7e\CrisInfo;

use Backend;
use Event;
use System\Classes\PluginBase;

/**
 * cris Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Массив, содержажий плагины из официального репозитария, 
     * необходимые для работы данного плагина
     *
     * @var array
     */
    public $require = [
        'Bree7e.Cris'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'CRIS Info',
            'description' => 'Информационные компоненты системы учёта публикаций',
            'author'      => 'Alexandr Vetrov',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            Components\Phones::class            => 'phones',
            Components\Statistics::class        => 'statistics',
            Components\Structure::class         => 'structure'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [];
    }
}