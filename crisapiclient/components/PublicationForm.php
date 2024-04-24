<?php namespace Bree7e\CrisApiClient\Components;

use Flash;
use Redirect;
use Request;
use Session;
use Validator;
use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Bree7e\Cris\Models\Publication;

class PublicationForm extends ComponentBase
{
    public $publication;

    public function componentDetails()
    {
        return [
            'name'        => 'Форма публикации',
            'description' => 'Форма для создания/редактирования публикации'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'Страница подтверждения',
                'description' => 'Страница привязки авторов к публикации',
                'type'        => 'dropdown',
                'default'     => '- none -',
                'required'    => true
            ]            
        ];        
    }

    public function getRedirectOptions()
    {
        return ['' => '- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    
    
    public function prepareData()
    {
        $this->publication = Session::get('publication');
    }

    public function onRun()
    {
        $this->addJs('assets/js/calendar-to-publication-form.js');
        $this->prepareData();
    }

    public function addBasicData()
    {
        $publication = new Publication();
        $attributes = Request::get('Publication');

        // Дата из модуля Semantic UI Calendar должна быть преобразована к 
        // стандартному виду date string (Y-m-d)
        // @see https://laravel.com/docs/5.5/eloquent-mutators#date-mutators
        if ($attributes['published_at']) { 
            $attributes['published_at'] = Carbon::createFromFormat('d.m.Y', $attributes['published_at'])->toDateString();
        }
        $publication->fill($attributes);


        // $methods = get_class_methods($publication);
        // $parents = class_parents($publication);
        
        /*
        * Redirect to the confirmation page after successful validate data
        */
        $redirectUrl = $this->pageUrl($this->property('redirect'))
            ?: $this->property('redirect');
        
        $json = $publication->toJson();
        Session::put('publication', $json);
        
        // нужно реализовать базовую валидацию, если прошла то редиректить 
        // на добавление авторов
        
        if (true) {
            return Redirect::to($redirectUrl);
            // return Redirect::to($redirectUrl)->with('publication', (string) $publication);
        } else {
            return Redirect::back()->withInput();
        }        


    }
}
