<?php namespace Bree7e\CrisApiClient\Components;

use Auth;
use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Publication;

class MyPublications extends ComponentBase
{
    public $publications;
    public $addedPublications;

    public function componentDetails()
    {
        return [
            'name'        => 'Мои публикации',
            'description' => 'Привязанные и добавленные публикации'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function prepareData()
    {   
        // Get the signed in user
        $frontEndUser = Auth::getUser();

        if (!$frontEndUser) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $this->publications = Publication::ofAuthors([$frontEndUser->id])
            ->orderBy('year','desc')
            ->get();

        $this->addedPublications = Publication::addedByAuthor($frontEndUser->id)->get();
    }

    public function onRun()
    {
        $this->prepareData();
    }

}
