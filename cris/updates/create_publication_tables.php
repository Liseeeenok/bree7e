<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePublicationTables extends Migration
{
    public function up()
    {   
        /**
         * Типы публикаций
         * Статья, тезисы, книга, диссертация, отчет, авторское св-во, патент.
         */
        Schema::create('bree7e_cris_publication_types', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();    
        });

        /**
         * Тип: Текст, карты, электронный ресурс. в СЛУЧАЕ патента ПРОграмма для ЭВМ или База данных. Тип относится к Публикации
         */
        Schema::create('bree7e_cris_publications', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id'); // Incrementing ID (primary key) using a "UNSIGNED INTEGER" equivalent.
            $table->string('authors');
            $table->tinyInteger('authors_count')->unsigned()->nullable(); // TINYINT UNSIGNED - число от 0 до 255.
            $table->string('title', 350);
            $table->string('journal', 300)->nullable()->comment('Для InProceedings это booktitle');
            $table->smallInteger('year')->nullable(); 
            $table->smallInteger('reportYear')->nullable(); 
            $table->string('volume',10)->nullable();
            $table->string('number',10)->nullable();
            $table->string('month',30)->nullable();
            $table->string('url')->nullable();
            $table->string('pages',20)->nullable();
            $table->string('address')->nullable()->comment('Обычно пишут город, например: М:');
            $table->enum('type', ['Текст', 'Электронный ресурс','Карта','Программа для ЭВМ','База данных'])->default('Текст');
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable()->comment('Пример: Издание 3-е, исправленное');
            $table->enum('language', ['russian', 'english'])->nullable();
            $table->enum('classification', [
                'Монографии и главы в монографиях', 
                'Статьи в российских журналах', 
                'Статьи в зарубежных и переводных журналах', 
                'Статьи в сборниках трудов конференций', 
                'Тезисы докладов',
                'Электронные публикации',
                'Свидетельства о государственной регистрации объектов интеллектуальной собственности',
                '----------------------',
                'Публикации в материалах международных научных мероприятий',
                'Публикации в материалах российских научных мероприятий',
                'Публикации в журналах, зарегистрированных в Web of Science',
                'Публикации в журналах из перечня ВАК',
                'Публикация в российских  журналах из Переченя ВАК, издаваемых РАН или издательством МАИК ',
                'Публикации в зарегистрированных научных электронных изданиях',
                'Публикации в прочих журналах',
                'Другие публикации по вопросам профессиональной деятельности',
                'Зарегистрированные в установленном порядке научные отчеты',
                'Научно-популярные книги и статьи',
                'Патенты на объекты интеллектуальной собственности',
                'Статьи в научных сборниках и периодических научных изданиях',
                'Список публикаций в российских  журналах, включённых в Перечень ВАК для докторских диссертаций, издаваемых РАН или издательством МАИК ',
                'Список публикаций в других зарубежных  журналах',
                'Препринты',
                'Scopus',
                'РИНЦ'
            ])->nullable();

            

            $table->text('annotation')->nullable(); 
            $table->date('published_at')->nullable(); 
            $table->string('doi',100)->nullable();
            $table->string('eid',255)->nullable();
            $table->boolean('is_to_print')->default(false);
            $table->boolean('is_special')->default(false)->comment('Специальный выпуск');
            $table->boolean('is_wos')->default(false);
            $table->boolean('is_scopus')->default(false);
            $table->boolean('is_risc')->default(false);
            $table->boolean('is_editable')->default(true); // Флаг показыает возможность редактировать публикацию front-end пользователем
            $table->integer('publication_type_id')->unsigned()->index(); // Переводит INTEGER в беззнаковое число UNSIGNED
            $table->integer('added_by_rb_user_id')->nullable()->unsigned()->index()->default(0); // 0 - добавил backend user
            $table->string('notes')->nullable()->comment('Заметки');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bree7e_cris_publication_types');        
        Schema::dropIfExists('bree7e_cris_publications');
    }
}


