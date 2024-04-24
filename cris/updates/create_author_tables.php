<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use RainLab\User\Models\User;

class CreateAuthorTables extends Migration
{
    public function up()
    {
        /**
         * Синонимы авторов
         */
        Schema::create('bree7e_cris_author_alternative_names', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('lastname');
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->integer('rb_author_id')->unsigned()->index()->nullable()->comment('Синоним автора. user_id');            
        });

        /**
         * Добавляем поля к таблице users
         */
        if(!Schema::hasColumns('users', ['middlename', 'birthdate', 'office', 'phone', 'url', 'adviser_id',
        'consultant_id', 'thesis', 'asp_form', 'asp_programm', 'asp_specialization', 'asp_start', 'asp_finish']))
        {
            Schema::table('users', function(Blueprint $table)
            {
                $table->string('middlename')->nullable();
                $table->datetime('birthdate')->nullable();
                $table->string('office')->nullable()->comment('Кабинет');
                $table->text('phones')->nullable(); // json
                $table->string('url')->nullable();
                $table->integer('rb_adviser_id')->unsigned()->index()->nullable()->comment('Научный руководитель. user_id');
                $table->integer('rb_consultant_id')->unsigned()->index()->nullable()->comment('Научный консультант. user_id');
                $table->string('thesis')->nullable();
                $table->enum('asp_form', ['Очная', 'Заочная'])->nullable();
                $table->string('asp_programm')->nullable()->comment('Направление подготовки. Например, 09.06.01 «Информатика и вычислительная техника»');
                $table->string('asp_specialization')->nullable()->comment('Направленность (научная специальность). Например, 05.13.11 «Математическое и программное обеспечение вычислительных машин, комплексов и компьютерных сетей»');
                $table->datetime('asp_start')->nullable()->comment('Начало аспирантуры');
                $table->datetime('asp_finish')->nullable()->comment('Окончание аспирантуры');
                $table->integer('drupal_id')->unsigned()->nullable()->comment('Идентификатор пользователя uid из сстемы Drupal');                    
            }); 
        }  

        /**
         * Авторы публикаций, многие-ко-многим
         */
        Schema::create('bree7e_cris_authors_publications', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->integer('rb_author_id')->unsigned();
            $table->integer('publication_id')->unsigned();
            $table->primary(['rb_author_id', 'publication_id'], 'author_publication');  
            $table->timestamps();          
        });  


    }

    public function down()
    {
        Schema::dropIfExists('bree7e_cris_author_alternative_names');
        if (Schema::hasColumns('users', ['middlename', 'birthdate', 'office', 'phone', 'url', 'adviser_id',
        'consultant_id', 'thesis', 'asp_form', 'asp_programm', 'asp_specialization', 'asp_start', 'asp_finish']))
        {
            Schema::table('users', function(Blueprint $table)
            {
                $table->dropColumn('middlename');
                $table->dropColumn('birthdate');
                $table->dropColumn('office');
                $table->dropColumn('phones');
                $table->dropColumn('url');
                $table->dropColumn('rb_adviser_id');
                $table->dropColumn('rb_consultant_id');
                $table->dropColumn('thesis');
                $table->dropColumn('asp_form');
                $table->dropColumn('asp_programm');
                $table->dropColumn('asp_specialization');
                $table->dropColumn('asp_start');
                $table->dropColumn('asp_finish');  
                $table->dropColumn('drupal_id');  
            });   
        }     
        Schema::dropIfExists('bree7e_cris_authors_publications');
    }
}


