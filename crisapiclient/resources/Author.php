<?php
namespace Bree7e\CrisApiClient\Resources;

use Illuminate\Http\Resources\Json\Resource;

// https://medium.com/@dinotedesco/using-laravel-5-5-resources-to-create-your-own-json-api-formatted-api-2c6af5e4d0e8
class Author extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'authors',
            'id' => (string)$this->id,
            'attributes' => [
                'surname'=> $this->surname,
                'name'=> $this->name,
                'middlename'=> $this->middlename,
                'email'=> $this->email,
                'birthdate'=> $this->birthdate->format('Y-m-d'),
                'office'=> $this->office,
                'phones'=> $this->phones,
                'url'=> $this->url,
                'thesis'=> $this->thesis,
                'asp_form'=> $this->asp_form,
                'asp_programm'=> $this->asp_programm,
                'asp_specialization'=> $this->asp_specialization,
                'asp_start'=> $this->asp_start,
                'asp_finish'=> $this->asp_finish
            ],
            // 'relationships' => new ArticlesRelationshipResource($this),
            // 'rb_adviser_id'=> $this->rb_adviser_id,
            // 'rb_consultant_id'=> $this->rb_consultant_id,
            'links'         => [
                'self' => route('authors.show', ['authors' => $this->id]),
            ],            
        ];
    }
}
