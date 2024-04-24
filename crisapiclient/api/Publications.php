<?php namespace Bree7e\CrisApiClient\Api;

use Illuminate\Http\Request;
use Backend\Classes\Controller;
use Bree7e\Cris\Models\Publication;
use Bree7e\CrisApiClient\Resources\Publication as PublicationResource;
use Bree7e\CrisApiClient\Resources\PublicationCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Publications Back-end API Controller
 */
class Publications extends Controller
{
    protected $publications;

    public function __construct(Publication $publications)
    {
        parent::__construct();
        $this->publications = $publications;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageSize = input('per_page', 10);
        if ($pageSize > 100) $pageSize = 100;

        $publications = $this->publications->paginate($pageSize);

        $publications->transform(function (Publication $publication) {
            return new PublicationResource($publication);
        });

        return new PublicationCollection($publications);
        // return $publications;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $publication = $this->publications->findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'error' => [
                    'status_code' => 404,
                    'message' => "Publication $id not found",
                    'type' => 'ModelNotFoundException'
                ]
            ], 404);
        }

        PublicationResource::withoutWrapping();
        return new PublicationResource($publication);
    }

    /**
     * Поиск публикаций
     *
     * @param  \Illuminate\Http\Request
     */
    public function search(Request $request, $phrase)
    {   
        return 'Hello World. You phrase is "' . $phrase . '"';
    }
}
