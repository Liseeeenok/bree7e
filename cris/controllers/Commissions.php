<?php namespace Bree7e\Cris\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Project Types Back-end Controller
 */
class Commissions extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Bree7e.Cris', 'cris', 'commissions');
    }
}
