<?php namespace Bree7e\Cris\Models;

use Model;

/**
 * Department Model
 */
class Department extends Model
{
    /**
     * @see http://octobercms.com/docs/database/traits#nested-tree
     */
    use \October\Rain\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_departments';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'chieff' => 'Bree7e\Cris\Models\Author'
    ];
    public $belongsToMany = [
        'authors' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_authors_departments_positions', 
            'key' => 'department_id', 
            'otherKey' => 'rb_author_id' 
        ],
        'positions' => [
            'Bree7e\Cris\Models\Position',
            'table' => 'bree7e_cris_authors_departments_positions', 
            'key' => 'department_id' ,
            'otherKey' => 'position_id' 
        ]        
    ];      
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Фильтрация научных подразделений
     *
     */ 
    public function scopeIsScientific($query)
    {
        return $query->where('is_scientific', true);
    }    

}
