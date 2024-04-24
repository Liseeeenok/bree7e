<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class ProjectsAddNioktrNumberAndUrl extends Migration
{
    public function up()
    {
        Schema::table('bree7e_cris_projects', function($table)
        {
            $table->string('nioktr_number', 60)->nullable()->comment('Регистрационный номер ЦИТИС НИОКТР');
            $table->string('competition')->nullable()->comment('Конкурс');
            $table->string('url')->nullable();            
        });
    }
    
    public function down()
    {
        Schema::table('bree7e_cris_projects', function($table)
        {
            $table->dropColumn('nioktr_number');
            $table->dropColumn('competition');
            $table->dropColumn('url');
        });
    }
}