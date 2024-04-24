<?php namespace Bree7e\CrisReport\Components;

use Cms\Classes\ComponentBase;
use Bree7e\Cris\Models\Author;
use Bree7e\Cris\Models\Publication;

class Report33 extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Форма 3.3',
            'description' => 'Генерация отчета для печати'
        ];
    }

    public $authors;

    public function onRun() 
    {
        $this->authors = Author::orderBy('surname')->get();
        //$this->authors = Author::ofScientificDepartments()->orderBy('surname')->get();
    }

    public function onRenderReport()
    {
        $author = post('author');
        $fromYear = post('fromYear');
        $toYear = post('toYear');
        
        if ($author > 0) {
            // потенциальная проблема, вставляю то что отправил пользователь
            $choosedAuthor = Author::where('id', $author)->first();
            $query = Publication::ofAuthors([$author])->orderBy('year');
            
            if (!$toYear) $toYear = 2100;
            if (!$fromYear) $fromYear = 1900;
            $query = $query->whereBetween('year', [$fromYear, $toYear]);

            $publications = $query->get();
            $publications = $publications->each(function ($item, $key) {
                // просто 1 страница
                if (is_numeric($item->pages)) { 
                    $item->pagesNumber = "1 c.";
                    return;
                } 

                // диапазон через дефис (58-68)
                // $a = $item->pages;
                // $pieces = multiexplode(["",""], $item->pages);
                $pieces = preg_split( "/(-|–)/u", $item->pages );
                $firstPage = intval(array_shift($pieces));
                $lastPage = intval(array_pop($pieces));
                if ($firstPage > 0 and $lastPage > $firstPage) {
                    $pages = $lastPage - $firstPage + 1;
                    $item->pagesNumber = "$pages c.";
                } else {
                    $item->pagesNumber = $item->pages;
                }
            });
        } else {
            \Flash::error('Сотрудник не выбран');
            return;
        }

        if ($publications->isEmpty()) {
            unset($this->page['publications']);
        } else {
            $this->page['publications'] = $publications;
            $this->page['author'] = $choosedAuthor;
        }
    } 
}
