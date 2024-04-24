<?php namespace Bree7e\Cris\Controllers;

use Lang;
use Flash;
use Input;
use BackendMenu;
use Backend\Classes\Controller;
use Bree7e\Cris\Models\Publication;
use October\Rain\Exception\ApplicationException;
use Bree7e\Cris\Components\MyPublications;

/**
 * Problems Back-end Controller
 */
class Problems extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Bree7e.Cris', 'cris', 'problems');
    }

    public function duplicates() // <=== Action method
    {
        $this->pageTitle = 'Дубликаты публикаций';
        return $this->makePartial('duplicates');
    }     

    public function emtpyLinks()
    {
        $this->pageTitle = 'Ссылки на несуществующие публикации';
        return $this->makePartial('emtpylinks');
    }     

    public function tooMoreAuthors()
    {
        $this->pageTitle = 'Привязано больше авторов, чем указано в строке';
        return $this->makePartial('toomoreauthors');
    }     

    public function incorrectlang()
    {
        $this->pageTitle = 'Некорректно установлен язык';
        return $this->makePartial('incorrectlang');
    }     

    /**
     * Фильтр публикаций, по умолчанию на экране не должны выводится записи
     * Когда пользователь выбирает какой-нибудь фильтр, то записи отображаются
     * @see https://octobercms.com/forum/post/how-to-filter-a-list-controller
     *
     * @param [type] $query
     * @return void
     */
    public function listExtendQuery($query)
    {
        $this->widget->listFilter->scopes;
        $filter = $this->widget->listFilter;
        $scopes = $filter->getScopes();
        // если где-то установлено значение, то выходим
        foreach ($scopes as $scope) {
            if ($filter->getScopeValue($scope)) return;
        }
        $query->noPublication();
    }


    public function index()
    {
        $this->vars['classificationOptions'] = (new Publication())->getClassificationOptions();
        $this->asExtension('ListController')->index();
    }   

    /**
     * AJAX функция для копирования года в отчетный год выбранных публикаций
     *
     * @return void
     */
    public function index_onCopyReportYearFromYear() 
    {
        /*
         * Validate checked identifiers
         */
        $checkedIds = post('checked');

        if (!$checkedIds || !is_array($checkedIds) || !count($checkedIds)) {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
            return $this->listRefresh();
        }

        $publications = Publication::find($checkedIds);

        if (!$publications) {
            throw new ApplicationException('Не удалось найти выбранные публикации в базе данных');
        }
        

        $countPublications = $publications->count();
        if ($countPublications) {
            foreach ($publications as $p) {
                $p->reportYear = (int)$p->year;
                $p->save();
            }

            Flash::success('Года успешно скопированы для публикаций (' . $countPublications . ')');
        }
        else {
            Flash::error('Не удалось скопировать года');
        }
        
        return $this->listRefresh();  
              
    }
        
    /**
     * AJAX функция для установки языка выбранных публикаций
     *
     * @return void
     */
    public function index_onSetLanguage()
    {
        $checkedIds = post('checked');

        if (!$checkedIds || !is_array($checkedIds) || !count($checkedIds)) {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
            return $this->listRefresh();
        }

        $inputLanguage = Input::get('language');

        switch ($inputLanguage) {
            case '1':
                $language = 'russian';
                break;
            case '2':
                $language = 'english';
                break;
            default:
                throw new ApplicationException('Неверно указан идентификатор языка');
                break;
        }

        // $a = new Publication()->

        $publications = Publication::find($checkedIds);

        $countPublications = $publications->count();
        if ($countPublications) {
            foreach ($publications as $p) {
                $p->language = $language;
                $p->save();
            }

            Flash::success("Для публикаций ($countPublications) установлен язык $language");
        }
        else {
            Flash::error('Не удалось установить язык');
        }
        
        return $this->listRefresh();  
    }    
        
    /**
     * AJAX функция для установки классификации выбранных публикаций
     *
     * @return void
     */
    public function index_onSetClassification()
    {
        $checkedIds = post('checked');
        $inputClassification = post('classification');

        if (!$checkedIds || !is_array($checkedIds) || !count($checkedIds)) {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
            return $this->listRefresh();
        }

        switch ($inputClassification) {
            case '1':
                $classification = 'Монографии и главы в монографиях';
                break;
            case '2':
                $classification = 'Статьи в российских журналах';
                break;
            case '3':
                $classification = 'Статьи в зарубежных и переводных журналах';
                break;
            case '4':
                $classification = 'Статьи в сборниках трудов конференций';
                break;
            case '5':
                $classification = 'Тезисы докладов';
                break;
            case '6':
                $classification = 'Электронные публикации';
                break;
            case '7':
                $classification = 'Свидетельства о государственной регистрации объектов интеллектуальной собственности';
                break;
            default:
                throw new ApplicationException('Неверно указан тип классификации публикации');
                break;
        }

        $publications = Publication::find($checkedIds);

        $countPublications = $publications->count();
        if ($countPublications) {
            foreach ($publications as $p) {
                $p->classification = $classification;
                $p->save();
            }

            Flash::success("Для публикаций ($countPublications) установлена классификация \"$classification\"");
        }
        else {
            Flash::error('Не удалось установить классификацию');
        }
        
        return $this->listRefresh();  
    }   

    /**
     * AJAX функция для установки классификации исходя из типа и языка публикации
     *
     * @return void
     */
    public function index_onSetCorrectClassification()
    {
        $publications = Publication::all();
        $count = 0;
        
        foreach ($publications as $p) {
            if ($p->classification !== 'Монографии и главы в монографиях') continue;
            switch ($p->publication_type_id) {
                case '1': // articles
                    if ($p->type == 'Электронный ресурс') {
                        $p->classification = 'Электронные публикации';
                        break;
                    }
                    if ($p->language === 'russian') {
                        $p->classification = 'Статьи в российских журналах';
                    } else {
                        $p->classification = 'Статьи в зарубежных и переводных журналах';
                    }
                    break;
                case '2': // inproceedings
                    // ставится вручную
                    break;
                case '3': // patents
                    $p->classification = 'Свидетельства о государственной регистрации объектов интеллектуальной собственности';
                    break;
                case '4': // books
                case '5': // inbooks
                    $p->classification = 'Монографии и главы в монографиях';
                    break;
                case '6': // phdthesis
                    $p->classification = 'Диссертации';
                    break;
            }            
            $count++;
            $p->save();
        }

        Flash::success("Для ($count) публикации(-ий)  проставлена классификация"); 
    }    
}
