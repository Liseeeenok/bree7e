<?php namespace Bree7e\Cris\Components;

use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Project;
use October\Rain\Database\Collection;
use Cms\Classes\Page;
use October\Rain\Argon\Argon;

class ProjectList extends ComponentBase
{
    /**
     * @var October\Rain\Database\Collection Список проектов, когда не задан :id
     */
    public $projects;
    
    /**
     * @var October\Rain\Database\Collection Список проектов, без дат
     */
    public $projectsWithoutDate;

    /**
     * Reference to the page for render project.
     * @var string
     */    
    public $projectPage;

    public function componentDetails()
    {
        return [
            'name' => 'Список проектов',
            'description' => 'Отображение списка выполняемых преоктов, а также завершенных.',
        ];
    }

    public function defineProperties()
    {
        return [
            'projectPage' => [
                'title' => 'Страница проекта',
                'description' => 'Страница используемая для вывода отдельной публикации',
                'type' => 'dropdown',
                'default' => 'projects',
            ]          
        ];
    }

    public function getProjectPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    

    public function onRun()
    {
        $this->projectPage = $this->property('projectPage');
        $this->projects = Project::all()
            ->filter(function($p) { 
                return $p['start_year_date'];
            })
            ->sortByDesc('start_year_date')
            ->groupBy(function($p) {
                return Argon::parse($p->start_year_date)->format('Y');
            });
        $this->projectsWithoutDate = Project::all()
            ->filter(function($p) { 
                return $p['start_year_date'] === null;
            });
    }
}
