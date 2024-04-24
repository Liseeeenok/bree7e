<?php namespace Bree7e\Cris\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use October\Rain\Database\Collection;
use Bree7e\Cris\Models\Publication;

class PublicationList extends ComponentBase
{
    /**
     * @var October\Rain\Database\Collection Список публикаций, когда не задан :id
     */
    public $publications;
    
    /**
     * Reference to the page for render publication.
     * @var string
     */    
    public $publicationPage;

    public function componentDetails()
    {
        return [
            'name' => 'Список публикаций',
            'description' => 'Отображение списка последних публикаций',
        ];
    }

    public function defineProperties()
    {
        return [
            'publicationPage' => [
                'title' => 'Publication Page',
                'description' => 'Страница используемая для вывода отдельной публикации',
                'type' => 'dropdown',
                'default' => 'publications',
            ]          
        ];
    }  
    
    public function getPublicationPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    

    public function onRun()
    {
        $this->publications = $this->getPublications();
        $this->publicationPage = $this->property('publicationPage');        
    }


    /**
     * Возвращает список публикаций
     *
     * @return October\Rain\Database\Collection
     */
    public function getPublications(): Collection
    {
        return Publication::All();
    }
}
