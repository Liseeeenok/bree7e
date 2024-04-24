<?php namespace Bree7e\Cris\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Departments Back-end Controller
 */
class Departments extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ReorderController'        
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Bree7e.Cris', 'cris', 'departments');
    }
}
