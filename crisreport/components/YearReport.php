<?php namespace Bree7e\CrisReport\Components;

use Bree7e\Cris\Models\Publication;
use Cms\Classes\ComponentBase;
use Input;

class YearReport extends ComponentBase
{
    /**
     * @var Отчетный год, запрашиваемый у пользователя
     */
    public $year;

    /**
     * @var Collection Коллекция публикация для вывода на экран
     */
    public $publications;

    public function componentDetails()
    {
        return [
            'name' => 'YearReport Component',
            'description' => 'Годовой отчёт',
        ];
    }

    public function onMakeReport()
    {
        $this->year = Input::get('year');

        $publications = Publication::whereReportYear($this->year)
            ->orderBy('authors')
            ->with('publication_authors')
            ->get();

        // Порядок вывода:
        // 10 Монографии
        // 20 Статьи в российских журналах
        // 30 Статьи в зарубежных и переводных журналах
        // 40 Статьи в сборниках трудов конференций
        // 50 Тезисы докладов
        // 60 Электронные публикации
        // 70 Свидетельства о государственной регистрации программ

        foreach ($publications as $key => $p) {
            switch ($p->publication_type_id) {
                case '1': // articles
                    if ($p->type == 'Электронный ресурс') {
                        $p->classified = 'Электронные публикации';
                        $p->weight = 60;
                        break;
                    }
                    switch ($p->language) {
                        case 'russian':
                            $p->classified = 'Статьи в российских журналах';
                            $p->weight = 20;
                            break;
                        case 'english':
                            $p->classified = 'Статьи в зарубежных и переводных журналах';
                            $p->weight = 30;
                            break;
                    }
                    break;
                case '2': // inproceedings
                    switch ($p->classification) {
                        case 'Статьи в сборниках трудов конференций':
                            $p->weight = 40;
                            $p->classified = $p->classification;
                            break;
                        case 'Тезисы докладов':
                            $p->weight = 50;
                            $p->classified = $p->classification;
                            break;
                        default:
                            $p->weight = 100;
                            $p->classified = "Некорректно классификация материалов конференций ($p->classification)";
                            break;
                        }
                    break;
                case '3': // patents
                    $p->classified = 'Свидетельства о государственной регистрации объектов интеллектуальной собственности';
                    $p->weight = 70;
                    break;
                case '4': // books
                case '5': // inbooks
                    $p->classified = 'Монографии и главы в монографиях';
                    $p->weight = 10;
                    break;
                case '6': // phdthesis
                    $p->classified = 'Диссертации';
                    $p->weight = 15;
                    break;
            }
        }
        
        $publications = $publications->sortBy(function($row) {
            return sprintf('%-12s%s', $row->weight, $row->authors);
        });

        $this->publications = $publications;

        // $classified = new Collection();

    }

}
