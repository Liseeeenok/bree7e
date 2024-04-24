<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class ProjectType extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_project_types';

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
        'name' => 'required'
    ];    
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'project' => Project::class
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Возвращает идентификатор типа проекта по названию
     * РФФИ, РНФ, ФЦП, РГНФ, Хоздоговор, Программа СО РАН
     *
     * @param string $name
     * @return int id типа проекта
     */
    public static function getIdbyName(string $name) 
    {
        $project = self::select('id')->where('name', $name)->first();
        if ($project) {
            return $project->id;
        } else {
            return null;
        }
    }   
}
