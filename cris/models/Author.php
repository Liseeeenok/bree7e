<?php namespace Bree7e\Cris\Models;

use Bree7e\Cris\Models\Department;
use Bree7e\Cris\Models\AuthorAlternativeName as Synonym;
use October\Rain\Auth\Models\User as UserModel;
use October\Rain\Database\Collection;
use October\Rain\Support\Str;

/**
 * Author Model
 */
class Author extends UserModel
{
    public $hasMany = [
        'projects' => [
            'Bree7e\Cris\Models\Project',
            'key' => 'rb_user_id', // Ключ этой модели в таблице 1-М
        ],
        'synonyms' => [
            'Bree7e\Cris\Models\AuthorAlternativeName',
            'key' => 'rb_author_id',
        ],
    ];

    public function __construct() {

        parent::__construct();
        
        /**
         * The attributes that should be mutated to dates.
         *
         * @var array
         */
        $this->dates = array_merge($this->dates,  [
            'birthdate',
            'asp_start',
            'asp_finish'
        ]);
        /**
         * @var array List of attribute names which are json encoded and decoded from the database.
         */
        $this->jsonable = array_merge($this->jsonable, ['phones']);

        $this->belongsToMany = array_merge($this->belongsToMany, [
            'publications' => [
                'Bree7e\Cris\Models\Publication',
                'table' => 'bree7e_cris_authors_publications', // таблица многие-ко-многим
                'key' => 'rb_author_id', // Ключ этой модели в таблице М-М
                'otherKey' => 'publication_id',
            ],
            'projects_author' => [
                'Bree7e\Cris\Models\Project',
                'table' => 'bree7e_cris_authors_projects', // таблица многие-ко-многим
                'key' => 'rb_author_id', // Ключ этой модели в таблице М-М
                'otherKey' => 'project_id',
            ],
            'positions' => [
                'Bree7e\Cris\Models\Position',
                'table' => 'bree7e_cris_authors_departments_positions',
                'key' => 'rb_author_id',
                'otherKey' => 'position_id',
                'pivot' => 'department_id',
                'pivotModel' => 'Bree7e\Cris\Models\AuthorPositionPivot',
            ],
            'departments' => [
                'Bree7e\Cris\Models\Department',
                'table' => 'bree7e_cris_authors_departments_positions',
                'key' => 'rb_author_id',
                'otherKey' => 'department_id',
            ],
        ]);      
        
        $this->belongsTo = array_merge($this->belongsTo, [
            'adviser' => [
                'Bree7e\Cris\Models\Author',
                'key' => 'rb_adviser_id',
            ],
            'consultant' => [
                'Bree7e\Cris\Models\Author',
                'key' => 'rb_consultant_id',
            ],
        ]);    
            
    }
    
    /**
     * Фильтрация авторов определенного подразделения
     *
     * @param Query $query
     * @param Array $departmentIds
     * @return Query
     */
    public function scopeOfDepartments($query, $departmentIds)
    {
        return $query->whereHas('departments', function ($q) use ($departmentIds) {
            $q->whereIn('department_id', $departmentIds);
        });
    }

    /**
     * Фильтрация авторов научных подразделений
     */
    public function scopeOfScientificDepartments($query)
    {
        $departmentIds = Department::select('id')->isScientific()->get()->toArray();
        return $query->ofDepartments($departmentIds);
    }

    /**
     * Cоздание 2 основных синонимов автора
     *
     * @param int $id Идентификатор автора
     * @return void
     */
    public function generateSynonyms()
    {
        // main russian synonym
        $synonym = new Synonym();
        $synonym->lastname = $this->surname;
        $synonym->firstname = mb_substr($this->name, 0, 1, "UTF-8") . ".";
        $synonym->middlename = mb_substr($this->middlename, 0, 1, "UTF-8") . ".";
        $this->synonyms()->save($synonym);

        // main english synonym
        $synonym = new Synonym();
        // $synonym->author()->associate($this); // $synonym->rb_author_id = $this->getKey();
        $synonym->lastname = Str::ascii($this->surname);
        $synonym->firstname = mb_substr(Str::ascii($this->name), 0, 1, "UTF-8") . ".";
        $synonym->middlename = mb_substr(Str::ascii($this->middlename), 0, 1, "UTF-8") . ".";
        $this->synonyms()->save($synonym);

    }

    public static function generateAllSynonyms()
    {
        $authors = Author::all(); // почитать про глобальную область видмости self::all() или static::all()

        foreach ($authors as $author) {
            if ($author->synonyms->count() == 0) {
                $author->generateSynonyms();
            }
        }
    }

    /**
     * Возвращает коллекцию подобранных авторов
     *
     * @param string $fullName - строка вида "Иванов П.В."
     * @return \October\Rain\Database\Collection 
     */
    public static function getSuggestions(string $fullName): Collection
    {
        $trimFullName = preg_replace('/\s+/', '', $fullName);
        $result = new Collection();
        $authors = self::with('synonyms')->get();
        foreach ($authors as $a) {
            foreach ($a->synonyms as $s) {
                $halfSynonym = $s->lastname.$s->firstname;
                $fullSynonym = $halfSynonym.$s->middlename;
                if ($trimFullName === $halfSynonym || $trimFullName === $fullSynonym) {
                    $result->push($a);
                    break;
                }
            }
        }
        return $result;
    }
}
