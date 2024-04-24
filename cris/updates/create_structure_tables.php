<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Создаем таблицы отделов и должностей
 */
class CreatePositionsTable extends Migration
{
    public function up()
    {
        Schema::create('bree7e_cris_positions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('shortname')->unique()->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('drupal_id')->unsigned()->nullable()->comment('Идентификатор из системы Drupal');
            $table->timestamps();
        });

        Schema::create('bree7e_cris_departments', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('name_en')->unique()->nullable();
            $table->boolean('is_scientific')->default(true);
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();            
            $table->integer('rb_user_id')->unsigned()->index()->nullable()->comment('Руководитель подразделения');            
            $table->integer('drupal_id')->unsigned()->nullable()->comment('Идентификатор из системы Drupal');
            $table->timestamps();
        });

        /**
         * Должности сотрудников в отделах, многие-ко-многим, потому что есть совместители
         */
        Schema::create('bree7e_cris_authors_departments_positions', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->integer('rb_author_id')->unsigned();
            $table->integer('department_id')->unsigned()->default(0);
            $table->integer('position_id')->unsigned();
            $table->primary(['rb_author_id', 'department_id', 'position_id'], 'author_department_position');  
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bree7e_cris_positions');
        Schema::dropIfExists('bree7e_cris_departments');
        Schema::dropIfExists('bree7e_cris_authors_departments_positions');
    }
}