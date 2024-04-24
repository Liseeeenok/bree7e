<?php namespace Bree7e\Cris\Models;

use Model;
use Bree7e\Cris\Models\Author;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;

/**
 * Project Model
 */
class Project extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_projects';

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
        'name' => 'required',
    ]; 
    
    protected $dates = [
        'created_at',
        'updated_at',
        'start_year_date',
        'finish_year_date'
    ];    

    /**
     * @var array Relations
     */
    public $hasOne = [
        
    ];
    public $hasMany = [];
    public $belongsTo = [
        'projecttype' => [
            'Bree7e\Cris\Models\ProjectType', 
            'key' => 'project_type_id'
        ],
        'leader' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'rb_user_id'
        ]
    ];
    public $belongsToMany = [
        'publications' => [
            'Bree7e\Cris\Models\Publication',
            'table' => 'bree7e_cris_projects_publications'
        ],
        'project_authors' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_authors_projects', // таблица многие-ко-многим
            'key' => 'project_id', // The key parameter is the foreign key name of the model on which you are defining the relationship
            'otherKey' => 'rb_author_id' // Second foreign key name in the many-to-many table
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Фильтрация проектов по типу
     *
     * @param \October\Rain\Database\Builder $query
     * @param array $types
     * @return \October\Rain\Database\Builder
     */
    public function scopeOfType(Builder $query, array $types): Builder
    {
        return $query->whereIn('project_type_id', $types);
    }

    /**
     * Фильтрация публикаций определенных руководитлей
     *
     * @param Query $query
     * @param array|Collection $leaders - Массив индентификаторов руководителей
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfLeaders(Builder $query, $leaders): Builder
    {
        if ($leaders instanceof Collection) {
            $leaders = $leaders->pluck('id');
        }        
        return $query->whereIn('rb_user_id', $leaders);
    } 

    /**
     * Фильтрация публикаций определенных руководитлей
     *
     * @param Query $query
     * @param Author|int $leader - индентификатор руководителЯ
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfLeader(Builder $query, $leader): Builder
    {
        if ($leader instanceof Author) {
            $leader = $leader->id;
        }
        return $this->ofLeaders([$leader]);
    }     
}
