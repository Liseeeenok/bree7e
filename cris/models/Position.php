<?php namespace Bree7e\Cris\Models;

use Model;

/**
 * Position Model
 */
class Position extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_positions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

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
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'authors' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_authors_departments_positions', 
            'key' => 'position_id', 
            'otherKey' => 'rb_author_id',
            'pivot' => 'department_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
