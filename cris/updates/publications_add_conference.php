<?php namespace Bree7e\Cris\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class PublicationsAddСonference extends Migration
{
    public function up()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->string('conference')->nullable();
            $table->boolean('is_public_pdf')->default(false);
        });
    }
    
    public function down()
    {
        Schema::table('bree7e_cris_publications', function($table)
        {
            $table->dropColumn('conference');
            $table->dropColumn('is_public_pdf');
        });
    }
}