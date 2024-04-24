<?php namespace Bree7e\Cris\Models;

use Model;

/**
 * AuthorAlternativeName Model
 */
class AuthorAlternativeName extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_author_alternative_names';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'rb_author_id'
    ];

    /**
     * Правила валидации. 
     * Используется вместе с October\Rain\Database\Traits\Validation
     * http://octobercms.com/docs/services/validation
     *
     * @var array
     */
    public $rules = [
        'lastname' => 'required|min:2'
    ];    

    /**
     * Поля отсутсвуют в базе данных
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'author' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'rb_author_id'
        ],        
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
