<?php namespace Bree7e\CrisInfo\Components;

use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Publication;
use Bree7e\Cris\Models\Project;


class Statistics extends ComponentBase
{
    public $authorCount;
    public $publicationCount;
    public $articleCount;
    public $articleWosCount;
    public $articleScopusCount;
    public $articleRiscCount;
    public $inproceedingsCount;
    public $patentCount;
    public $projectCount;

    public function componentDetails()
    {
        return [
            'name'        => 'Statistics',
            'description' => 'Количество авторов и статей на сайте'
        ];
    }

    public function defineProperties()
    {
        return [];
    }
    
    public function onRun()
    {
        $this->authorCount = Author::all()->count();
        $this->publicationCount =   Publication::all()->count();
        $this->articleCount =       Publication::articles()->get()->count();
        $this->articleWosCount =    Publication::articles()->isWos()->get()->count();
        $this->articleScopusCount = Publication::articles()->isScopus()->get()->count();
        $this->articleRiscCount =   Publication::articles()->isRisc()->get()->count();

        $this->inproceedingsCount = Publication::inproceedings()->get()->count();
        $this->patentCount = Publication::patents()->get()->count();
        $this->projectCount = Project::all()->count();
    }    
}
