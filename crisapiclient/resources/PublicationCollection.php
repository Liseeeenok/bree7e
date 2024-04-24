<?php

namespace Bree7e\CrisApiClient\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PublicationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);

        // https://laravel.com/docs/5.6/eloquent-resources
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
        ];        
    }
}
