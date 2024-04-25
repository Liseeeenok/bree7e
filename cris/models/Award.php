<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class Award extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_awards';

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
        'institution' => 'required',
        'author' => 'required',
        'awardtype' => 'required'
    ];    
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'awardtype' => [
            'Bree7e\Cris\Models\AwardType', 
            'key' => 'id_award_type'
        ],
        'institution' => [
            'Bree7e\Cris\Models\Institution', 
            'key' => 'id_institution'
        ],
        'author' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'id_author'
        ]
    ];
    public $belongsToMany = [];
    public $attachOne = [];
    public $attachMany = [];  

    /**
     * Фильтрация наград по ведомству
     *
     * @param \October\Rain\Database\Builder $query
     * @param array $types
     * @return \October\Rain\Database\Builder
     */
    public function scopeOfInstitution(Builder $query, array $types): Builder
    {
        return $query->whereIn('id_institution', $types);
    }

    /**
     * Фильтрация наград по типу
     *
     * @param \October\Rain\Database\Builder $query
     * @param array $types
     * @return \October\Rain\Database\Builder
     */
    public function scopeOfType(Builder $query, array $types): Builder
    {
        return $query->whereIn('id_award_type', $types);
    }

    /**
     * Фильтрация наград по автору
     *
     * @param Query $query
     * @param array|Collection $leaders - Массив индентификаторов руководителей
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfAuthors(Builder $query, $authors): Builder
    {
        if ($authors instanceof Collection) {
            $authors = $authors->pluck('id');
        }        
        return $query->whereIn('id_author', $authors);
    } 
}
