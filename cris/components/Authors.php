<?php namespace Bree7e\Cris\Components;

use DB;
use Auth;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Project;
use Bree7e\Cris\Models\Publication;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use October\Rain\Argon\Argon;
use RainLab\User\Models\User;

class Authors extends ComponentBase
{   
    /**
     * @var Bree7e\Cris\Models\Author Автор
     */
    public $author;

    /**
     * @var array Публикации автора, сгруппированный по годам. Ключи массива - года
     */
    public $publications;

    /**
     * @var array Проекты автора, сгруппированный по годам. Ключи массива - проекты
     */
    public $projects;

    /**
     * @var array Проекты автора участие, сгруппированный по годам. Ключи массива - проекты
     */
    public $projectsPartGroupedByYear;

    /**
     * @var string Руководство аспирантами
     */
    public $advisering;

    /**
     * Reference to the page for render publication.
     * @var string
     */
    public $publicationPage;
    public $projectPage;

    public function componentDetails()
    {
        return [
            'name' => 'Деятельность автора',
            'description' => 'Список публикаций и проектов выбранного автора',
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'Id',
                'description' => 'Идентификатор автора',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'default' => '{{ :id }}',
            ],
            'publicationPage' => [
                'title' => 'Страница публикации',
                'description' => 'Страница используемая для вывода отдельной публикации',
                'type' => 'dropdown',
                'default' => 'publications',
            ],
            'projectPage' => [
                'title' => 'Страница проекта',
                'description' => 'Страница используемая для вывода отдельного проекта',
                'type' => 'dropdown',
                'default' => 'projects',
            ],
        ];
    }

    public function getPublicationPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getProjectPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->getData();
        $this->publicationPage = $this->property('publicationPage');
        $this->projectPage = $this->property('projectPage');
    }

    public function getData()
    {
        $id = $this->property('id');

        try {
            $author = Author::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return $this->controller->run('404');
        }

        // наследуемая модель не может получить доступ к attachOne родителя
        $user = User::findOrFail($id);
        $author->avatar = $user->avatar;

        $publications = Publication::ofAuthors([$id])->orderBy('year', 'desc')->get();
        $author->publicationCount = $publications->count();
        $publicationsGroupedByYear = $publications->groupBy('year');

        $projects = Project::ofLeader($author)->orderBy('start_year_date', 'desc')->get();
        $author->projectCount = $projects->count();
        $projectsGroupedByYear = $projects->groupBy(function($p) {
            return Argon::parse($p->start_year_date)->format('Y');
        });
        
        $projectsPartId = DB::table('bree7e_cris_authors_projects')->where('rb_author_id', $author->id)->pluck('project_id');
        $projectsPart = DB::table('bree7e_cris_projects')->whereIn('id', $projectsPartId)->orderBy('start_year_date', 'desc')->get();
        $projectsPartGroupedByYear = $projectsPart->groupBy(function($p) {
            return Argon::parse($p->start_year_date)->format('Y');
        });
        
        $this->projectsPartGroupedByYear = $projectsPartGroupedByYear;
        $this->author = $author;
        $this->publications = $publicationsGroupedByYear;
        $this->projects = $projectsGroupedByYear;

    }

}
