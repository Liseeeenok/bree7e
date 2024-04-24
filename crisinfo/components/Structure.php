<?php namespace Bree7e\CrisInfo\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Department;
use Bree7e\Cris\Models\Publication;
use October\Rain\Database\TreeCollection;

class Structure extends ComponentBase
{
    public $departments;

    /**
     * @var string Ссылка на страницу об авторе
     */    
    public $profilePageUrl;    

    public function componentDetails()
    {
        return [
            'name'        => 'Structure',
            'description' => 'Структурный состав сотрудников института'
        ];
    }

    public function defineProperties()
    {
        return [
            'profilePage' => [
                'title' => 'Profile Page',
                'description' => 'Страница используемая для вывода отдельной публикации',
                'type' => 'dropdown',
                'default' => 'publications',
            ]          
        ];
    }

    public function getProfilePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    

    public function onRun()
    {
        $this->getDepartments();
        $profilePage = $this->property('profilePage');        
    }

    /**
     * Проставляет веса каждому автору подразделения в поле 
     * positionSum исходя из суммы весов его должностей
     *
     * @param TreeCollection|Department $d
     * @return void
     */
    private function setAuthorWeight($department)
    {
        if ($department instanceOf TreeCollection) { // root
            $department->each(function ($d) {
                $this->setAuthorWeight($d);
            });
        } 
        
        if ($department instanceOf Department) {
            if ($department->getChildCount() > 0) {
                $department->getChildren()->each(function ($d) {
                    $this->setAuthorWeight($d);
                });
            } elseif ($department->authors->count() > 0) {
                $department->authors->each(function ($a) {
                    $a->positionSum = $a->positions->pluck('sort_order')->sum();
                });    
                $department->sortedAuthors = $department->authors->sortBy('positionSum');
            }
        }
    }

    public function getDepartments() 
    {
        $departments = Department::siblings()->with('authors')->with('positions')->get();

        $this->setAuthorWeight($departments);

        // foreach ($departments as $department) {
        //     $department->authors = $department->authors->sortBy('positionSum');
        // }
        $this->departments = $departments;
    }
  
}
