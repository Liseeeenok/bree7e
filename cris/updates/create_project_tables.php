<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProjectTables extends Migration
{
    public function up()
    {
        Schema::create('bree7e_cris_projects', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 350);
            $table->text('description');
            $table->integer('project_type_id')->unsigned();
            $table->integer('rb_user_id')->nullable()->unsigned()->comment('Руководитель проекта, user_id');
            $table->smallInteger('start_year')->unsigned()->nullable(); // SMALLINT UNSIGNED - число от 0 до 65535.
            $table->smallInteger('finish_year')->unsigned()->nullable(); // SMALLINT UNSIGNED - число от 0 до 65535.
            $table->timestamps();
        }); // 

        /**
         * Типы проектов: РФФИ, РНФ, РГНФ и т.д.
         */
        Schema::create('bree7e_cris_project_types', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });    
          
        Schema::create('bree7e_cris_projects_publications', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->integer('project_id')->unsigned();
            $table->integer('publication_id')->unsigned();
            $table->primary(['project_id', 'publication_id'], 'project_publication');            
        });          
    }

    public function down()
    {
        Schema::dropIfExists('bree7e_cris_projects_publications');
        Schema::dropIfExists('bree7e_cris_project_types');        
        Schema::dropIfExists('bree7e_cris_projects');
    }   
}
