<?php namespace Bree7e\Cris\Updates;

use Schema;
use Bree7e\Cris\Models\ProjectType;
use Bree7e\Cris\Models\Project;
use Bree7e\Cris\Models\Position;
use Bree7e\Cris\Models\Department;
use Bree7e\Cris\Models\PublicationType;
use October\Rain\Database\Updates\Seeder;

class SeedTypesTable extends Seeder
{
    public function run()
    {
        // 1 article (AUTHOR, TITLE, JOURNAL, YEAR) + (VOLUME, NUMBER, PAGES, MONTH, NOTE, KEY)
        PublicationType::create(['name' => 'Статья в журнале']);
        
        // 2 inproceedings (AUTHOR/EDITOR, TITLE, PUBLISHER, YEAR) + (VOLUME, SERIES, ADDRESS, EDITION, MONTH, NOTE, KEY, PAGES)
        PublicationType::create(['name' => 'Материал конференции']);
        
        // 3 patent
        PublicationType::create(['name' => 'Авторское свидетельство']);
        
        // 4 book (AUTHOR/EDITOR, TITLE, PUBLISHER, YEAR) + (VOLUME, SERIES, ADDRESS, EDITION, MONTH, NOTE, KEY, PAGES)
        PublicationType::create(['name' => 'Книга']);

        // 5 inbook (AUTHOR/EDITOR, TITLE, CHAPTER/PAGES, PUBLISHER, YEAR) + 
        PublicationType::create(['name' => 'Часть книги']);

        // 6 phdthesis (AUTHOR, TITLE, SCHOOL, YEAR) + (ADDRESS, MONTH, NOTE, KEY)
        PublicationType::create(['name' => 'Диссертация']);

        // 
        PublicationType::create(['name' => 'Учебное пособие']);

        Position::create([
            'name' => 'Директор',
            'shortname' => 'Директор',
            'sort_order' => '100',
            'drupal_id' => '6',
        ]);
        Position::create([
            'name' => 'Заместитель директора',
            'shortname' => 'Зам.дир.',
            'sort_order' => '50',
            'drupal_id' => '103',
        ]);
        Position::create([
            'name' => 'Ученый секретарь',
            'shortname' => 'Уч.сек.',
            'sort_order' => '30',
            'drupal_id' => '97',
        ]);
        Position::create([
            'name' => 'Заведующий отделением',
            'shortname' => 'Зав.отд.',
            'sort_order' => '20',
            'drupal_id' => '4',
        ]);
        Position::create([
            'name' => 'Заведующий лабораторией',
            'shortname' => 'Зав.лаб.',
            'sort_order' => '15',
            'drupal_id' => '134',
        ]);
        Position::create([
            'name' => 'Главный научный сотрудник',
            'shortname' => 'Г.Н.С.',
            'sort_order' => '10',
            'drupal_id' => '115',
            ]);
        Position::create([
            'name' => 'Ведущий научный сотрудник',
            'shortname' => 'В.Н.С.',
            'sort_order' => '9',
            'drupal_id' => '3',
        ]);
        Position::create([
            'name' => 'Старший научный сотрудник',
            'shortname' => 'С.Н.С.',
            'sort_order' => '8',
            'drupal_id' => '2',
        ]);
        Position::create([
            'name' => 'Научный сотрудник',
            'shortname' => 'Н.С.',
            'sort_order' => '7',
            'drupal_id' => '1',
        ]);
        Position::create([
            'name' => 'Младший научный сотрудник',
            'shortname' => 'М.Н.С.',
            'sort_order' => '6',
            'drupal_id' => '112',
        ]);
        Position::create([
            'name' => 'Программист',
            'shortname' => 'Прогр.',
            'sort_order' => '5',
            'drupal_id' => '126',
        ]);

        Department::create(['name' => 'Дирекция','drupal_id' => '14', 'is_scientific' => false]);

        $root  = Department::create(['name' => 'Отделение 1 Эволюционных уравнений и управляемых динамических систем','drupal_id' => '24']);
        $child = Department::create(['name' => 'Лаб.1.1 Дифференциальных уравнений и управляемых систем','drupal_id' => '25']);
        $child->makeChildOf($root);
        $child = Department::create(['name' => 'Лаб.1.2 Оптимального управления ','drupal_id' => '26']);
        $child->makeChildOf($root);

        Department::create(['name' => 'Отделение 2 Прикладных проблем математической физики и теории поля','drupal_id' => '27']);

        Department::create(['name' => 'Отделение 3 Динамических свойств и управления сложными объектами в пространстве','drupal_id' => '31']);
        $root =  Department::create(['name' => 'Отделение 4 Информационных технологий и систем','drupal_id' => '34']);
        $child = Department::create(['name' => 'Лаб.4.1 Комплексных информационных систем','drupal_id' => '35']);
        $child->makeChildOf($root);
        $child = Department::create(['name' => 'Лаб.4.2 Информационно-телекоммуникационных технологий исследования техногенной безопасности','drupal_id' => '36']);
        $child->makeChildOf($root);
        
        $root =  Department::create(['name' => 'Отделение 5 Вычислительных и управляющих систем','drupal_id' => '31']);
        $child = Department::create(['name' => 'Лаб.5.1 Параллельных и распределённых вычислительных систем','drupal_id' => '32']);
        $child->makeChildOf($root);
        $child = Department::create(['name' => 'Лаб.5.2 Информационно-управляющих систем ','drupal_id' => '33']);
        $child->makeChildOf($root);
        
        $root = Department::create(['name' => 'Отделение 6 Методов невыпуклой и комбинаторной оптимизации','drupal_id' => '']);
        $child = Department::create(['name' => 'Лаб.6.1 Невьmуклой оптимизации ','drupal_id' => '']);
        $child->makeChildOf($root);
        $child = Department::create(['name' => 'Лаб.6.2 Логических и оптимизационных методов анализа сложных систем','drupal_id' => '']);
        $child->makeChildOf($root);
        
        ProjectType::create(['name' => 'РФФИ']);
        ProjectType::create(['name' => 'РНФ']);
        ProjectType::create(['name' => 'ФЦП']);
        ProjectType::create(['name' => 'РГНФ']);
        ProjectType::create(['name' => 'Хоздоговор']);
        ProjectType::create(['name' => 'Программа СО РАН']);
        ProjectType::create(['name' => 'ФАНО']);
    }
}
