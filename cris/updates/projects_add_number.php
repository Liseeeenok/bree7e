<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class ProjectsAddNumber extends Migration
{
    public function up()
    {
        Schema::table('bree7e_cris_projects', function($table)
        {
            $table->string('project_number', 100)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('bree7e_cris_projects', function($table)
        {
            $table->dropColumn('project_number');
        });
    }
}