<?php namespace Bree7e\CrisInfo\Components;

use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Author;

class Phones extends ComponentBase
{
    public $authors;
    
    public function componentDetails()
    {
        return [
            'name'        => 'Phones',
            'description' => 'Телефонный справочник'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun() 
    {
        $this->getAuthors();
    }    

    public function getAuthors() 
    {
        $this->authors = Author::orderBy('surname')->get();
        $a = $this->authors[1];
        // vardump($a->phones);
    }      
}
