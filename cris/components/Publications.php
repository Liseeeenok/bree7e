<?php namespace Bree7e\Cris\Components;

use Bree7e\Cris\Models\Publication;
use Cms\Classes\ComponentBase;
use Cms\Classes\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Publications extends ComponentBase
{
    /**
     * @var Bree7e\Cris\Models\Publication Публикация, выводимая на экран.
     */
    public $publication;

    public function componentDetails()
    {
        return [
            'name' => 'Публикация',
            'description' => 'Отображение публикации в разных вариантах',
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'Id',
                'description' => 'Идентификатор публикации',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'default' => '{{ :id }}',
            ]
        ];
    }

    public function onRun()
    {
        $this->publication = $this->getPublication();
    }

    /**
     * Возвращает публикацию
     *
     * @return Bree7e\Cris\Models\Publication
     */
    public function getPublication()
    {
        $id = $this->property('id');
        try {
            $publication = Publication::findOrFail($id);
            return $publication;
        } catch (ModelNotFoundException $ex) {
            return $this->controller->run('404');
        }
    }
}
