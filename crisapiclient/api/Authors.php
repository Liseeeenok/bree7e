<?php namespace Bree7e\CrisApiClient\Api;

use Illuminate\Http\Request;
use Backend\Classes\Controller;
use Bree7e\Cris\Models\Author;
use Bree7e\CrisApiClient\Resources\Author as AuthorResource;
// use Bree7e\Cris\Resources\PublicationCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Authors Back-end API Controller
 */
class Authors extends Controller
{
    protected $authors;

    public function __construct(Author $authors)
    {
        parent::__construct();
        $this->authors = $authors;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageSize = input('per_page', 50);
        if ($pageSize > 100) $pageSize = 100;

        $authors = $this->authors->paginate($pageSize);

        $authors->transform(function (Author $author) {
            return new AuthorResource($author);
        });

        return $authors;
        // return new PublicationCollection($authors);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $author = $this->authors->findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'error' => [
                    'status_code' => 404,
                    'message' => "Author $id not found",
                    'type' => 'ModelNotFoundException'
                ]
            ], 404);
        }

        return $author;

        PublicationResource::withoutWrapping();
        return new PublicationResource($author);
    }

}
