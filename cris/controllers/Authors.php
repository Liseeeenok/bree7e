<?php namespace Bree7e\Cris\Controllers;

use Backend;
use BackendMenu;
use Backend\Classes\Controller;
use Bree7e\Cris\Models\Author;
use Flash;
use Redirect;

/**
 * Authors Back-end Controller
 */
class Authors extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ImportExportController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Bree7e.Cris', 'cris', 'authors');
    }

    /**
     * Overriding default controller Update action
     *
     * @param [type] $authorId
     * @param [type] $context
     * @return void
     */
    public function update($authorId, $context = null)
    {
        // Вместо неявного params[0] внутри view
        $this->vars['authorId'] = $authorId;
        // Call the FormController behavior update() method
        return $this->asExtension('FormController')->update($authorId, $context);
    }

    /**
     * AJAX Action, создание 2 основных синонимов автора
     *
     * @param string $id Идентификатор автора
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_onGenerateAuthorSynonyms($id = null)
    {
        if ($id == null) {
            // Flash::error('Не указан идентификатор автора');
            return Redirect::to(Backend::url('bree7e/cris/authors'))->with('message', 'Не указан идентификатор автора');
        }

        $id = intval($id);
        if (!is_int($id) || ($id < 1)) {
            Flash::error('Указан некорректный идентификатор автора');
            return Redirect::back();
        }

        $author = Author::findOrFail($id);
        $author->generateSynonyms();

        $this->initRelation($author); // Для поведения отношения, используемого в Bree7e\Cris\Controllers\Authors не определена модель.
        Flash::success('Синонимы успешно добавлены');
        return $this->relationRefresh('synonyms');
    }

    public function index_onGenerateAllSynonyms()
    {
        Author::generateAllSynonyms();
        return Flash::success('Синонимы для всех авторов успешно добавлены');
    }

}
