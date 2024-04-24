<?php namespace Bree7e\Cris\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Database\Schema\Blueprint;


class AuthorsAddExcept extends Migration
{
    public function up()
    {
        if(!Schema::hasColumns('users', ['is_except']))
        {
            Schema::table('users', function(Blueprint $table)
            {
                $table->boolean('is_except')->default(false)->comment('Исключить из расчётов ПРНД');
            }); 
        }  
    }
    
    public function down()
    {
        if (Schema::hasColumns('users', ['is_except']))
        {
            Schema::table('users', function(Blueprint $table)
            {
                $table->dropColumn('is_except');
            });   
        }     
    }
}