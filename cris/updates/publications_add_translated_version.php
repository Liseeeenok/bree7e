<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class PublicationsAddTranslatedVersion extends Migration
{
    public function up()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->integer('translated_id')->unsigned()->index()->nullable()->comment('Идентификатор переводной версии публикации');
        });
    }
    
    public function down()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->dropColumn('translated_id');
        });
    }
}