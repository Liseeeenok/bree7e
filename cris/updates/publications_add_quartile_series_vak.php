<?php namespace Bree7e\Cris\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class PublicationsAddQuartileSeriesVak extends Migration
{
    public function up()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->enum('quartile', ['Q1','Q2','Q3','Q4','Q5'])->nullable()->comment('Квартиль. Q5 означает не Core Collection');
            $table->string('series')->nullable();
            $table->boolean('is_vak')->default(false);
        });
        DB::statement("ALTER TABLE bree7e_cris_publications CHANGE COLUMN language language ENUM('russian','english','german','spanish','italian','mongolian','chinese','kazakh','another') NOT NULL DEFAULT 'russian' AFTER edition;");
    }
    
    public function down()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->dropColumn('quartile');
            $table->dropColumn('series');
            $table->dropColumn('is_vak');
        });
        DB::statement("ALTER TABLE bree7e_cris_publications CHANGE COLUMN language language ENUM('russian','english') NOT NULL DEFAULT 'russian' AFTER edition;");
    }
}