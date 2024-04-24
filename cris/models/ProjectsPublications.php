<?php namespace Bree7e\Cris\Models;

use DB;
use Model;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Department;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use Bree7e\Cris\Models\AuthorAlternativeName as Synonym;

/**
 * Publication Model
 */
class ProjectsPublications extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_projects_publications';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
    ];

    /**
     * @var array Правила валидации для Traits\Validation
     */
    public $rules = [
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public $customMessages = [
    ];

    protected $dates = [
    ];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
    ];
    public $belongsToMany = [
    ];
 
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'paper' => ['System\Models\File', 'public' => false]
    ];
    public $attachMany = [];
}