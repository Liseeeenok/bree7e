<?php namespace Bree7e\Cris;

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
        'RainLab.User'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'CRIS',
            'description' => 'Система учёта публикаций',
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
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Event::listen('backend.menu.extendItems', function($manager)
        {
            $manager->addSideMenuItems('RainLab.User', 'user', [
                'bree7e.cris.import' => [
                    'label'       => 'Импорт авторов',
                    'url'         => Backend::url('bree7e/cris/authors/import'),
                    'icon'        => 'icon-sign-in',
                    'permissions' => ['bree7e.cris.import_authors'],
                    'order'       => 200,
                ]
            ]);
        });    
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            Components\PublicationSearch::class => 'publicationSearch',
            Components\Publications::class      => 'publications',
            Components\PublicationList::class   => 'publicationList',
            Components\Projects::class          => 'projects',
            Components\ProjectList::class       => 'projectList',
            Components\Authors::class           => 'authors',
            Components\PersonalAccounts::class  => 'personalAccounts'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'bree7e.cris.access_projects' => [
                'tab' => 'Current Research Information System',
                'label' => 'Управление проектами'
            ],
            'bree7e.cris.access_publicationtypes' => [
                'tab' => 'Current Research Information System',
                'label' => 'Управление типом публикаций'
            ],
            'bree7e.cris.access_authors' => [
                'tab' => 'Current Research Information System',
                'label' => 'Управление авторами'
            ],
            'bree7e.cris.access_departments' => [
                'tab' => 'Current Research Information System',
                'label' => 'Управление отделами'
            ],
            'bree7e.cris.access_positions' => [
                'tab' => 'Current Research Information System',
                'label' => 'Управление должностями'
            ],
            'bree7e.cris.import_authors' => [
                'tab' => 'Current Research Information System',
                'label' => 'Импорт авторов'
            ],
            'bree7e.cris.import_publications' => [
                'tab' => 'Current Research Information System',
                'label' => 'Импорт публикаций'
            ],
            'bree7e.cris.import_projects' => [
                'tab' => 'Current Research Information System',
                'label' => 'Импорт проектов'
            ]
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'cris' => [
                'label'       => 'CRIS',
                'url'         => Backend::url('bree7e/cris/publications'),
                'icon'        => 'icon-book',
                'permissions' => ['bree7e.cris.*'],
                'order'       => 300,
                'sideMenu'    => [
                    'publications' => [
                        'label'       => 'Публикации',
                        'icon'        => 'icon-book',
                        'url'         => Backend::url('bree7e/cris/publications'),
                        'permissions' => ['bree7e.cris.*']
                    ],
                    'projects' => [
                        'label'       => 'Проекты',
                        'icon'        => 'icon-folder',
                        'url'         => Backend::url('bree7e/cris/projects'),
                        'permissions' => ['bree7e.cris.access_projects']
                    ],
                    'projecttype' => [
                        'label'       => 'Типы проектов',
                        'icon'        => 'icon-folder-o',
                        'url'         => Backend::url('bree7e/cris/projecttypes'),
                        'permissions' => ['bree7e.cris.access_projects']
                    ],
                    'awards' => [
                        'label'       => 'Награды',
                        'icon'        => 'icon-trophy',
                        'url'         => Backend::url('bree7e/cris/awards'),
                        'permissions' => ['bree7e.cris.access_projects']
                    ],
                    'awardtypes' => [
                        'label'       => 'Типы наград',
                        'icon'        => 'icon-trophy',
                        'url'         => Backend::url('bree7e/cris/awardtypes'),
                        'permissions' => ['bree7e.cris.access_projects']
                    ],
                    'institutions' => [
                        'label'       => 'Ведомства',
                        'icon'        => 'icon-university',
                        'url'         => Backend::url('bree7e/cris/institutions'),
                        'permissions' => ['bree7e.cris.access_projects']
                    ],
                    'authors' => [
                        'label'       => 'Авторы',
                        'icon'        => 'icon-pencil',
                        'url'         => Backend::url('bree7e/cris/authors'),
                        'permissions' => ['bree7e.cris.access_authors']
                    ],
                    'departments' => [
                        'label'       => 'Отделы',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('bree7e/cris/departments'),
                        'permissions' => ['bree7e.cris.access_departments']
                    ],
                    'positions' => [
                        'label'       => 'Должности',
                        'icon'        => 'icon-shield',
                        'url'         => Backend::url('bree7e/cris/positions'),
                        'permissions' => ['bree7e.cris.access_positions']
                    ],
                    'problems' => [
                        'label'       => 'Проблемы',
                        'icon'        => 'icon-exclamation-triangle',
                        'url'         => Backend::url('bree7e/cris/problems'),
                        'permissions' => ['bree7e.cris.*']
                    ],
                ]                
            ],
        ];
    }
}