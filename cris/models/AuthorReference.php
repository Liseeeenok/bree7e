<?php namespace Bree7e\Cris\Models;

use Model;
use October\Rain\Database\Builder;

/**
 * ProjectType Model
 */
class AuthorReference extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_author_references';

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
        'authors_str' => 'required',
        'positions_str' => 'nullable',
        'publicationtype' => 'required',
        'full_name_publication' => 'required',
        'count_pages' => 'nullable',
        'count_images' => 'nullable',
        'is_literary_sources' => 'nullable',
        'is_information_not_rospatent' => 'nullable',
        'is_information_other_people' => 'nullable',
        'is_information_inventions' => 'nullable',
        'inventions' => 'nullable',
        'is_lock' => 'nullable',
        'NIR' => 'nullable',
        'information' => 'nullable',
        'id_author' => 'nullable',
        'id_zav_lab_otdel' => 'nullable',
    ]; 

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'publicationtype' => [
            'Bree7e\Cris\Models\PublicationType', 
            'key' => 'id_publication_type'
        ],
        'commission' => [
            'Bree7e\Cris\Models\Commission', 
            'key' => 'id_commission'
        ],
        'commissionmember' => [
            'Bree7e\Cris\Models\CommissionMember', 
            'key' => 'id_commission_member'
        ],
        'author' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'id_author'
        ],
        'zav_lab_otdel' => [
            'Bree7e\Cris\Models\Author',
            'key' => 'id_zav_lab_otdel'
        ]
    ];
    public $belongsToMany = [];
    public $attachOne = [];
    public $attachMany = [];  
}
