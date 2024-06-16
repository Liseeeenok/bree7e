<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class Docx extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_docx';

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
        'name' => 'nullable',
        'id_author' => 'nullable',
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
