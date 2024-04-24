<?php namespace Bree7e\Cris\Components;

use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\{Project, Publication};

class Projects extends ComponentBase
{
    public $project;
    public $publications;
    
    public function componentDetails()
    {
        return [
            'name'        => 'Отдельный проект',
            'description' => 'Отображение информации о проекте: номер, руководитель, аннотация, годы выполнения. Связанные публикации.'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'Id',
                'description' => 'Идентификатор проекта',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'default' => '{{ :id }}',
            ]
        ];
    }

    public function onRun()
    {
        $this->project = $this->getProject();
    }

    /**
     * Возвращает проект или выкидывает 404
     *
     * @return Bree7e\Cris\Models\Project
     */
    public function getProject()
    {
        $id = $this->property('id');
        try {
            $project = Project::findOrFail($id);
            return $project;
        } catch (ModelNotFoundException $ex) {
            return $this->controller->run('404');
        }
    }

    public function getPublications()
    {
        $id = $this->property('id');
        $sql = Publication::ofProject($id)->toSql();
        $this->publications = Publication::ofProject($id)->get();
        $b = 1;
    }
}

