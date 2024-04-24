<?php namespace Bree7e\Cris\Models;

use DB;
use Model;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Department;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use Bree7e\Cris\Models\AuthorAlternativeName as Synonym;

/**
 * Publication Model
 */
class Publication extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bree7e_cris_publications';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [
        'id',
        'authors_count',
        'added_by_rb_user_id',
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
    ];

    /**
     * @var array Правила валидации для Traits\Validation
     */
    public $rules = [
        'authors' => 'required',
        'title' => 'required',
        'publicationtype' => 'required'
        // publication_authors тоже надо будет добавить
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public $customMessages = [
        // 'required' => 'Поле :attribute обязательно',
        'authors.required' => 'Необходимо заполнить поле "Авторы".',
        'title.required' => 'Необходимо заполнить поле "Название публикации".'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at'
    ];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'translated_version' => [
            Publication::class, 
            'key' => 'translated_id'
        ],
        'publicationtype' => [
            PublicationType::class, 
            'key' => 'publication_type_id'
        ],
        'addedByAuthor' => [
            Author::class, 
            'key' => 'added_by_rb_user_id'
        ],
        'addedBySpeaker' => [
            Author::class,
            'key' => 'speaker'
        ],
    ];
    public $belongsToMany = [
        'projects' => [
            'Bree7e\Cris\Models\Project',
            'table' => 'bree7e_cris_projects_publications', // таблица многие-ко-многим
            'order' => 'name'
        ],
        'publication_authors' => [
            'Bree7e\Cris\Models\Author',
            'table' => 'bree7e_cris_authors_publications', // таблица многие-ко-многим
            'key' => 'publication_id', // The key parameter is the foreign key name of the model on which you are defining the relationship
            'otherKey' => 'rb_author_id' // Second foreign key name in the many-to-many table
        ]
    ];
 
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'paper' => ['System\Models\File', 'public' => false]
    ];
    public $attachMany = [];

    /**
     * Сохранение нужной классификации по типу публикации
     * Вручную классификация проставляется только для Трудов конференций
     */
    public function beforeSave()
    {   
        switch ($this->publication_type_id) {
            case '1': // articles
                if ($this->type == 'Электронный ресурс') {
                    $this->classification = 'Электронные публикации';
                    break;
                }
                if ($this->language === 'russian') {
                    $this->classification = 'Статьи в российских журналах';
                } else {
                    $this->classification = 'Статьи в зарубежных и переводных журналах';
                }
                break;
            case '2': // inproceedings
                // ставится вручную
                break;
            case '3': // patents
                $this->classification = 'Свидетельства о государственной регистрации объектов интеллектуальной собственности';
                break;
            case '4': // books
            case '5': // inbooks
                $this->classification = 'Монографии и главы в монографиях';
                break;
            case '6': // phdthesis
                $this->classification = 'Диссертации';
                break;
        }
    }

    /**
     * Наивысшая индексация. WoS по квартилям >  Scopus > РИНЦ
     *
     * @return void
     */
    public function getBestIndexationAttribute()
    {
        if ($this->is_wos) {
            if (!$this->quartile) return 'Web of Science';
            switch ($this->quartile) {
                case 'Q1':
                    return 'Web of Science Q1';
                    break;
                case 'Q2':
                    return 'Web of Science Q2';
                    break;
                case 'Q3':
                    return 'Web of Science Q3';
                    break;
                case 'Q4':
                    return 'Web of Science Q4';
                    break;                
                case 'Q5':
                    return 'Web of Science Emerging Sources Citation Index';
                    break;                
            }
        }

        if ($this->is_scopus) {
            if (!$this->quartile_scopus) return 'Scopus';
            switch ($this->quartile_scopus) {
                case 'Q1':
                    return 'Scopus Q1';
                    break;
                case 'Q2':
                    return 'Scopus Q2';
                    break;
                case 'Q3':
                    return 'Scopus Q3';
                    break;
                case 'Q4':
                    return 'Scopus Q4';
                    break;                
                case 'Q5':
                    return 'Scopus Emerging Sources Citation Index';
                    break;                
            }
        }

        if ($this->is_wl) {
            if (!$this->quartile_wl) return 'Белый список';
            switch ($this->quartile_wl) {
                case 'УБС1':
                    return 'Уровень белого списка 1';
                    break;
                case 'УБС2':
                    return 'Уровень белого списка 2';
                    break;
                case 'УБС3':
                    return 'Уровень белого списка 3';
                    break;
                case 'УБС4':
                    return 'Уровень белого списка 4';
                    break;                               
            }
        }
        if ($this->is_risc && $this->is_vak) return 'ВАК + РИНЦ';
        if ($this->is_risc) return 'РИНЦ';
        if ($this->is_vak) return 'ВАК';
        return '-';
    }

    /**
     * Фильтрация публикаций по отчетному году
     *
     * @param Builder $query
     * @param int $year
     * @return \October\Rain\Database\Builder
     */
    public function scopeWhereReportYear(Builder $query, int $year): Builder
    {
        return $query->where('reportYear', $year);
    }

    /**
     * Фильтрация публикаций по отчетным годам
     *
     * @param Builder $query
     * @param int $year
     * @return \October\Rain\Database\Builder
     */

     /**
      * Фильтрация публикаций по диапазону отчётных годов
      *
      * @param Builder $query
      * @param integer $startYear
      * @param integer $finishYear
      * @return \October\Rain\Database\Builder
      */
    public function scopeWhereReportYearBetween(Builder $query, int $startYear = 0, int $finishYear = 2100): Builder
    {
        return $query->whereBetween('reportYear', [$startYear, $finishYear]);
    }

    /**
     * Фильтрация публикаций по годам
     *
     * @param Builder $query
     * @param array $years
     * @return \October\Rain\Database\Builder
     */
    public function scopeWhereYears(Builder $query, array $years): Builder
    {
        return $query->whereIn('year', $years);
    }

    /**
     * Фильтрация публикаций по годам
     *
     * @param Builder $query
     * @param integer $firstyear первый год
     * @param integer $lastyear последний год
     * @return \October\Rain\Database\Builder
     */
    public function scopeBetweenYears(Builder $query, $firstyear = 1900, $lastyear = 2100): Builder
    {
        return $query->whereBetween('year', [$firstyear, $lastyear]);
    }

    /**
     * Фильтрация публикаций без указания отчетного года
     *
     * @param Builder $query
     * @return \October\Rain\Database\Builder
     */
    public function scopeNoReportYear(Builder $query): Builder
    {
        return $query->where('reportYear', NULL);
    }

    /**
     * Фильтрация публикаций без указания года
     *
     * @param Builder $query
     * @return \October\Rain\Database\Builder
     */
    public function scopeNoYear(Builder $query): Builder
    {
        return $query->where('year', NULL);
    }

    /**
     * Фильтрация публикаций без привязанных авторов
     *
     * @param Builder $query
     * @return \October\Rain\Database\Builder
     */
    public function scopeNoAuthors(Builder $query): Builder
    {
        return $query->has('publication_authors', '=', '0');
    }

    /**
     * Вернуть 0 публикаций. Для случая проблем это экран по-умолчанию.
     *
     * @param Builder $query
     * @return \October\Rain\Database\Builder
     */
    public function scopeNoPublication(Builder $query): Builder
    {
        return $query->where('id', '<', '0');
    }

    /**
     * Фильтрация публикаций определенного подразделения
     *
     * @param Query $query
     * @param [type] $departmentId
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfDepartment(Builder $query, $departmentId): Builder
    {
        $departments = Department::findOrFail($departmentId)
            ->getAllChildrenAndSelf()
            ->pluck('id')
            ->toArray();

        $authors = Author::ofDepartments($departments)->get();
        // получить массив идентификатов авторов
        // @see https://laravel.com/docs/5.5/collections#method-pluck
        $authorIds = $authors->pluck('id')->toArray();

        $query = $query->ofAuthors($authorIds);

        return $query;
    }

    /**
     * Фильтрация публикаций определенного проекта
     *
     * @param Query $query
     * @param array $projectId - Индентификатор проекта
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfProject(Builder $query, $projectId): Builder
    {
        return $query->whereHas('projects', function($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });
    }

    /**
     * Фильтрация публикаций определенного автора/авторов
     *
     * @param Query $query
     * @param array $authors - Массив индентификаторов
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeOfAuthors(Builder $query, $authorIds): Builder
    {
        // http://php.net/manual/ru/functions.anonymous.php
        return $query->whereHas('publication_authors', function($q) use ($authorIds) {
            $q->whereIn('rb_author_id', $authorIds);
        });
    }

    /**
     * Фильтрация публикаций добавленных определнным пользователем
     *
     * @param Query $query
     * @param array $authors - Массив индентификаторов
     * @return \October\Rain\Database\Builder
     */ 
    public function scopeAddedByAuthors(Builder $query, array $authors): Builder
    {
        return $query->whereIn('added_by_rb_user_id', $authors);
    }

    /**
     * Фильтрация публикаций по типу
     *
     * @param Builder $query
     * @param array $types
     * @return Builder
     */
    public function scopeFilterByType(Builder $query, array $types): Builder
    {
        return $query->whereIn('publication_type_id', $types);
    }    

    /**
     * Фильтрация публикаций, только статьи в журналах
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeArticles(Builder $query): Builder
    {
        return $query->where('publication_type_id', 1);
    }

    /**
     * Фильтрация публикаций, только статьи в журналах
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeInproceedings(Builder $query): Builder
    {
        return $query->where('publication_type_id', 2);
    }

    /**
     * Фильтрация публикаций, только авторские свидетельства
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopePatents(Builder $query): Builder
    {
        return $query->where('publication_type_id', 3);
    }

    /**
     * Фильтрация публикаций по типам индексации
     *
     * @param Builder $query
     * @param array $types wos | scopus | risc | vak
     * @return Builder
     */
    public function scopeOfIndexationTypes(Builder $query, array $types): Builder
    {
        foreach ($types as $type) {
            switch ($type) {
                case 'wos':
                    $query = $query->orWhere('is_wos', 1);
                    break;
                case 'scopus':
                    $query = $query->orWhere('is_scopus', 1);
                    break;
                case 'risc':
                    $query = $query->orWhere('is_risc', 1);
                    break;
                case 'vak':
                    $query = $query->orWhere('is_vak', 1);
                    break;
            }
        }
        return $query;
    }

    /**
     * Фильтрация публикаций, индексируемых WoS
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeIsWos(Builder $query): Builder
    {
        return $query->where('is_wos', 1);
    }

    /**
     * Фильтрация публикаций, индексируемых Scopus
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeIsScopus(Builder $query): Builder
    {
        return $query->where('is_scopus', 1);
    }

    /**
     * Фильтрация публикаций, индексируемых РИНЦ
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeIsRisc(Builder $query): Builder
    {
        return $query->where('is_risc', 1);
    }

    /**
     * Фильтрация публикаций, входящих в перечень ВАК
     *
     * @param Builder $query
     * @return October\Rain\Database\Builder
     */
    public function scopeIsVak(Builder $query): Builder
    {
        return $query->where('is_vak', 1);
    }

    /**
     * Возвращает коллекцию автоматически подобранных авторов, исходя из заполненого поля 'authors'
     * Ключ - часть строки, значение - автор из базы данных типа \Bree7e\Cris\Models\Author
     *
     * @return \October\Rain\Database\Collection 
     */
    public function getAuthorSuggestions(): Collection
    {                        
        // разобрать авторов и убрать пробелы
        $authorsArray = array_map('trim', explode(',',$this->authors));
        $this->authors_count = count($authorsArray);

        $result = new Collection();
        $synonymsCollection = Synonym::all();
        
        foreach ($authorsArray as $publicationAuthor) {
            // сравнение строк без пробелов
            $trimPublicationAuthor = preg_replace('/\s+/', '', $publicationAuthor);
            foreach ($synonymsCollection as $synonym) {
                // TODO собрать неполные синонимы, такие как "Ветров А."" и просто "Ветров"
                $fullSynonym = $synonym->lastname.$synonym->firstname.$synonym->middlename;
                if ($trimPublicationAuthor == $fullSynonym) {
                    $result->put($publicationAuthor, $synonym);
                    break;
                }
                // если не нашли, то вернуть null
                $result->put($publicationAuthor, null);
            }
        }
        return $result;
    }

    /**
     * Привязать выбранных авторов к публикации
     *
     * @param array $authorsIds - массив идентификаторов авторов
     * @return void
     */
    public function addAuthors(array $authorsIds = null)
    {
        $this->publication_authors()->sync($authorsIds);
    }
    

    /**
     * Автоматическое добавление авторов к публикации на основе строки "Авторы"
     *
     * @return int|boolean Возвращает количество добавленных авторов или false
     */
    public function autoAddAuthors()
    {
        // https://laravel.com/docs/5.5/eloquent-relationships#updating-many-to-many-relationships
        // https://stackoverflow.com/questions/24702640/laravel-save-update-many-to-many-relationship

        $relatedIds = [];

        $suggestions = $this->getAuthorSuggestions();
        foreach ($suggestions as $s) { // мы получаем синоним, по нему надо достать автора
            if ($s) {
                $relatedIds[] = $s->rb_author_id;
            }
            // $this->publication_authors()->attach($s->rb_author_id);
        }

        // сохранить количество авторов
        $this->save(); 

        $countAuthors = count($relatedIds);
        if ($countAuthors === 0) return false;

        $this->addAuthors($relatedIds);
        return $countAuthors;
    }    

    /**
     * Автоматически добавляет авторов к публикациям, где не привязаны авторы
     *
     * @return number Количество публикаций, к которым были привязаны авторы
     */
    public static function addAutoAllAuthors(): int
    {
        $count = 0;
        $publications = Publication::all(); 
            foreach ($publications as $pub) {
                if ($pub->publication_authors->count() == 0) {
                    $count++;
                    $pub->autoAddAuthors();
                }
            }
        return $count;
    }

    /**
     * Возвращает список доступных опций классификаций публикаций
     * для годового отчёта
     * Метод используется в фильтре лист-контроллера Публикаций
     * @return array Массив строк классификаций.
     */
    public function getClassificationOptions()
    {
        $result = [];

        $type = DB::select(DB::raw('SHOW COLUMNS FROM bree7e_cris_publications WHERE field = "classification"'))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = array();
        foreach(explode(',', $matches[1]) as $value){
            $values[] = trim($value, "'");
        }
        $n = array_search('----------------------', $values);
        $slice = array_slice($values, 0, $n);;
        $result = array_combine($slice, $slice);;

        return $result;
    }    
}