<?php namespace Bree7e\Cris\Updates;

use Bree7e\Cris\Models\ProjectType;
use Bree7e\Cris\Models\PublicationType;
use October\Rain\Database\Updates\Seeder;

class SeedTypesTable extends Seeder
{
    public function run()
    {
        // 8 article (AUTHOR, TITLE, JOURNAL, YEAR) + (VOLUME, NUMBER, PAGES, MONTH, NOTE, KEY)
        PublicationType::create(['name' => 'Статья в сборнике статей']);
        ProjectType::create(['name' => 'ФАНО']);
    }
}
