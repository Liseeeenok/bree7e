<?php namespace Bree7e\Cris\Controllers;

use BackendMenu;
use Flash;
use Request;
use Redirect;
use Backend\Classes\Controller;
use Bree7e\Cris\Models\Publication;
use ApplicationException;


/**
 * Publications Back-end Controller
 */
class Publications extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    private $import;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Bree7e.Cris', 'cris', 'publications');
        $this->addJs(url('plugins/bree7e/cris/assets/js/display-fields-errors.js'));
    }

    public function import()    // <=== Action method
    {
        $this->pageTitle = 'Импорт публикаций';

        // $requiredPermissions = ['bree7e.cris.import_publications'];

        return $this->makePartial('import');
    } 

    /**
     * AJAX function для создания авторов на основе строки Авторы
     *
     * @param string $pubId
     * @return void
     */
    public function update_onAutoAddAuthors(string $pubId = null)
    {
        $pubId = intval($pubId);
        if (!is_int($pubId) || ($pubId < 1)) {
            Flash::error('Указан некорректный идентификатор публикации');
            return Redirect::back();
        }        

        $pub = Publication::findOrFail($pubId);
        $addingResult = $pub->autoAddAuthors();

        $this->initRelation($pub); // Для поведения отношения, используемого в Bree7e\Cris\Controllers\Authors не определена модель.
        if ($addingResult > 0 ) {
            Flash::success('Авторы успешно добавлены ('. $addingResult . ')');
        } else {
            Flash::error('Не удалось добавить авторов');
        }
        return $this->relationRefresh('publication_authors');        
    }    

    public function index_onAddAutoAllAuthors()
    {
        $count = Publication::addAutoAllAuthors();
        if ($count > 0) {
            Flash::success("Авторы успешно добавлены к $count публикации(-ям)");
        } elseif ($count === 0) {
            Flash::info("Не найдено публикаций, к которым можно добавить авторов");
        }
        
    }

    public function onRecognize() 
    {
        $import = input('import');

        switch ($import['publication_type_id']) {
            case '1': // articles
                switch ($import['language']) {
                    case 'russian':
                        $pattern = '/([0-9]+\.)\s\t?((?:[а-яёА-Я\-]+\s?[А-Я]{1}\.\s?[А-Я]{1}\.,?\s)+)(.*)\s\/\/\s([\S ]*)(\d{4})[\.,]\s(?:Т\.\s(\d+))?[\.,]?\s?(?:№\s?([\d\(\)\- )]+)[\.,])?\s?[cCсС]\.\s?(\d+(?:[-–]\d+)?)/u';
                        $pattern = '/([0-9]+\.)?\s\t?(?<authors>(?:[а-яёА-Я\-]+\s?[А-Я]{1}\.\s?[А-Я]{1}\.,?\s)+)(?<title>.*)\s\/\/\s(?<journal>[\S ]*)(?<year>\d{4})[\.,]\s(?:Т\.\s(?<volume>\d+))?[\.,]?\s?(?:№\s?(?<number>[\d\(\)\- )]+)[\.,])?\s?[cCсС]\.\s?(?<pages>\d+(?:[-–]\d+)?)(?:.*?\((?<index>.*)\))?/u';
                        break;
                    
                    case 'english':
                        $pattern = '/([0-9]+\.)\s\t?((?:[a-zA-Zа-яёА-Я\-]+\s?([A-ZА-Я]{1}\.)?\s?[A-ZА-Я]{1}\.,?\s)+)(.*)\s\/\/\s([\S ]*)(\d{4})[\.,]\s(?:[Т|Vol]\.\s(\d+))?[\.,]?\s?(?:[№|No]\s?([\d\(\)\- )]+)[\.,])?\s?[cCсС]|[p]\.\s?(\d+(?:[-–]\d+)?)/u';
                        $pattern = '/([0-9]+\.)\s\t?(?<authors>(?:[a-zA-Zа-яёА-Я\-]+\s?(?:[A-ZА-Я]{1}\.)?\s?[A-ZА-Я]{1}\.,?\s)+)(?<title>.*)\s\/\/\s(?<journal>[\S ]*)(?<year>\d{4})[\.,]\s?(?:(?:Vol|[TТт])\.\s(?<volume>\d+))?[\.,]?\s?(?:(?:No\.|№)\s?(?<number>[\d\(\)\- )]+)[\.,])?\s?[рРpPCcСс]{1,2}\.\s?(?<pages>\d+(?:[-–]\d+)?)(?:.*?\((?<index>.*)\))?/u';
                        break;
                    
                    default:
                        throw new ApplicationException('Неподдерживаемый язык статей');
                        break;
                }
                break;
            
            case '2': // inproceedings
                switch ($import['language']) {
                    case 'russian':
                        $pattern = '/([0-9]+\.)?\s\t?(?<authors>(?:[а-яёА-Я\-]+\s?[А-Я]{1}\.\s?[А-Я]{1}\.,?\s)+)(?<title>.*)\s\/\/\s(?<journal>[\S ]*)(?:\.\s(?<address>[\S ]*))(?<year>\d{4})[\.,]\s(?:Т\.\s(?<volume>\d+))?[\.,]?\s?(?:№\s?(?<number>[\d\(\)\- )]+)[\.,])?\s?[cCсС]\.\s?(?<pages>\d+(?:[-–]\d+)?)(?:.*?\((?<index>.*)\))?/u';                        
                    break;

                    case 'english':
                        $pattern = '/([0-9]+\.)\s\t?(?<authors>(?:[a-zA-Zа-яёА-Я\-]+\s?(?:[A-ZА-Я]{1}\.)?\s?[A-ZА-Я]{1}\.,?\s)+)(?<title>.*)\s\/\/\s(?<journal>[\S ]*)\.\s(?<address>[\S ]*)?(?:\:\s(?<publisher>[\S ]*))?(?<year>\d{4})[\.,]\s?(?:(?:Vol|[TТт])\.\s(?<volume>\d+))?[\.,]?\s?(?:(?:No\.|№|Issue)\s?(?<number>[\d\(\)\- )]+)[\.,])?\s?(?:[рРpPCcСс]{1,2}\.\s?(?<pages>\d+(?:[-–]\d+)?))?(?:.*\s?(?<doi>10\.\d{3,5}\/.*))?(?:.*?\((?<index>.*)\))?/u';
                    break;

                    default:
                        throw new ApplicationException('Неподдерживаемый язык материалов конференций');
                        break;
                }
                break;

            default:
                throw new ApplicationException('Неподдерживаемый тип публикации');
                break;
        }      

        $text = $import['text'];

        $matches = 0;
        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER, 0);

        if ($matches) {
            $this->vars['publications'] = $matches;
            $this->vars['language'] = $import['language'];
            $this->vars['notes'] = $import['notes'];
            $this->vars['publication_type_id'] = $import['publication_type_id'];
            $this->vars['classification'] = $import['classification'];
            $this->vars['reportYear'] = $import['reportYear'];

            switch ($import['publication_type_id']) {
                case '1': // articles
                    return [
                        '#result' => $this->makePartial('import_list')
                    ];    
                    break;
                case '2': // inproceedings
                    return [
                        '#result' => $this->makePartial('import_list_proc')
                    ];    
                break;
            }
        } else {
            return Flash::error('Публикации не распознаны');
        }
    }

    public function onImportPublications() 
    {
        $publications = Request::input('publications');
        $language = Request::input('language');
        $notes = Request::input('notes');
        $publication_type_id = Request::input('publication_type_id');
        $classification = Request::input('classification');
        $reportYear = Request::input('reportYear');

        foreach ($publications as $p) {
            if (isset($p['is_wos']) and $p['is_wos'] == "on")  $p['is_wos'] = 1;
            if (isset($p['is_scopus']) and $p['is_scopus'] == "on") $p['is_scopus'] = 1;
            if (isset($p['is_risc']) and $p['is_risc'] == "on") $p['is_risc'] = 1;

            switch ($classification) {
                case '1': 
                    $c = 'Монографии и главы в монографиях';
                    break;
                case '2': 
                    $c = 'Статьи в российских журналах';
                    break;
                case '3': 
                    $c = 'Статьи в зарубежных и переводных журналах';
                    break;
                case '4': 
                    $c = 'Статьи в сборниках трудов конференций';
                    break;
                case '5': 
                    $c = 'Тезисы докладов';
                    break;
                case '6': 
                    $c = 'Электронные публикации';
                    break;
                case '7': 
                    $c = 'Свидетельства о государственной регистрации объектов интеллектуальной собственности            ';
                    break;
                default:
                    # code...
                    break;
            }

            $publication = new Publication();
            $publication->fill($p);
            $publication->classification = $c;
            $publication->publication_type_id = $publication_type_id;
            $publication->language =  $language;
            $publication->notes = $notes;
            $publication->reportYear = $reportYear;
            $publication->save();
        }

        return Redirect::to('backend/bree7e/cris/publications')->with('message', 'Публикации добавлены');
    }
}
