<?php namespace Bree7e\Cris\Models;

use Model;

/**
 * PublicationType Model
 */
class PublicationType extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_publication_types';

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
    public $hasOne = [
        'publication' => ['Bree7e\Cris\Models\Publication']
    ];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
