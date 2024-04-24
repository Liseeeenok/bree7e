<?php namespace Bree7e\CrisApiClient\Components;

use Auth;
use Redirect;
use Request;
use Session;
use Log;
use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Publication;

class AddAuthorsForm extends ComponentBase
{
    public $authorsString;
    public $suggestAuthors;
    public $sessionData;

    public function componentDetails()
    {
        return [
            'name'        => 'Форма привязки авторов',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->prepareData();
    }

    /**
     * Велосипед по десериализации
     *
     * @param String $s
     * @return Publication
     */
    private function decodePublication(String $jsonString = null): Publication
    {
        $result = new Publication();
        if ($jsonString == '') return $result;
        
        $publicationObject = json_decode($jsonString);     
        foreach ($publicationObject as $key => $value) {
            $result->{$key} = $value;
        }

        return $result;
    }

    public function prepareData()
    {
        $s = Session::get('publication');    
        if ($s === null) return;
        $this->sessionData = $s;

        $publication = $this->decodePublication($s);
        $this->authorsString = $publication->authors;
        $this->suggestAuthors = $publication->getAuthorSuggestions();


    }

    public function readdAuthorSuggestions($authorString)
    {
        # code...
        // перепривязка авторов
        return;
    }
    
    protected function savePublication()
    {
        $jsonData = Request::get('Publication');
        $oldSessionData = $jsonData['session'];
        $publication = $this->decodePublication($oldSessionData);
        $publication->authors = $jsonData['authors'];

        $authorsIds = $jsonData['authorsIds'];
        $publication->authors_count = count($authorsIds) ? count($authorsIds) : 1;

        // removes all NULL, FALSE and Empty Strings but leaves 0 (zero) values
        $authorsIds = array_filter( $authorsIds, 'strlen' );        

        $publication->added_by_rb_user_id = Auth::getUser();

        $publication->save();
        $publication->addAuthors($authorsIds);
        
        try {    
            Session::forget('publication');
            return Redirect::to('profile');
        } catch (\Exception $ex) {
            Flash::error($ex->getMessage());
            Log::error($ex->getMessage());
        } 
        # code...
    }
}
