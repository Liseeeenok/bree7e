<?php
namespace Bree7e\CrisApiClient\Resources;

use Illuminate\Http\Resources\Json\Resource;

// https://medium.com/@dinotedesco/using-laravel-5-5-resources-to-create-your-own-json-api-formatted-api-2c6af5e4d0e8
class Publication extends Resource
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
            'type' => 'publications',
            'id' => (string)$this->id,
            'attributes' => [
                'title' => $this->title,
            ],
            // 'relationships' => new ArticlesRelationshipResource($this),
            'links'         => [
                'self' => route('publications.show', ['publication' => $this->id]),
            ],            
        ];
    }
}