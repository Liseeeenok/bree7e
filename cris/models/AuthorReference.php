<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class AuthorReference extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_author_references';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];
    
    /**
     * @var array Rules
     */
    public $rules = [
        'authors_str' => 'required',
        'positions_str' => 'required',
        'id_publication_type' => 'required',
        'full_name_publication' => 'required',
        'count_pages' => 'required',
        'count_images' => 'required',
        'is_literary_sources' => 'required',
        'is_information_not_rospatent' => 'required',
        'is_information_other_people' => 'required',
        'is_information_inventions' => 'required',
        'inventions' => 'required',
        'is_lock' => 'required',
        'NIR' => 'required',
        'information' => 'required',
    ]; 

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $attachOne = [];
    public $attachMany = [];  
}
