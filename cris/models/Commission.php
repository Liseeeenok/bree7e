<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class Commission extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_commissions';

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
        'president' => 'required',
        'inspector' => 'required'
    ];    
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
      'president' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'id_president'
      ],
      'inspector' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'id_inspector'
      ]
    ];
    public $belongsToMany = [
      'commission_members' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_commission_members',
            'key' => 'id_commission',
            'otherKey' => 'id_member'
      ],
      'commission_patent_members' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_commission_patent_members',
            'key' => 'id_commission',
            'otherKey' => 'id_patent_member'
      ]
    ];
    public $attachOne = [];
    public $attachMany = [];  
}
