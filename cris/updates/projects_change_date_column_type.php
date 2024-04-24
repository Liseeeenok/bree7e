<?php namespace Bree7e\Cris\Updates;

use Bree7e\Cris\Models\Project;
use October\Rain\Argon\Argon;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;
use DB;

class ProjectsChangeDateColumnType extends Migration
{
    public function up()
    {
        
        Schema::table('bree7e_cris_projects', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->date('start_year_date')->nullable();
            $table->date('finish_year_date')->nullable();
        });


        // Schema::table('bree7e_cris_projects', function ($table) { $table->text('description')->nullable()->change(); });

        /*
         * Convert number years to date format
         */
        $projects = Project::all();
        foreach ($projects as $project) {
            if ($project->start_year > 0) {
                $project->start_year_date = Argon::createFromDate($project->start_year, 1, 1); // 01.01.start_year
            }
            if ($project->finish_year > 0) {
                $project->finish_year_date = Argon::createFromDate($project->finish_year, 12, 31); // 31.12.finish_year
            }
            $project->save();
        }

        Schema::table('bree7e_cris_projects', function (Blueprint $table) {
            $table->dropColumn('start_year');
            $table->dropColumn('finish_year');
        });

    }

    public function down()
    {

        Schema::table('bree7e_cris_projects', function (Blueprint $table) {
            $table->smallInteger('start_year')->unsigned()->nullable();
            $table->smallInteger('finish_year')->unsigned()->nullable();
        });

        $projects = Project::all();
        foreach ($projects as $project) {
            if ($project->start_year_date) {
                $project->start_year = $project->start_year_date->year;
            }
            if ($project->finish_year_date) {
                $project->finish_year = $project->finish_year_date->year;
            }
            $project->save();
        }

        Schema::table('bree7e_cris_projects', function (Blueprint $table) {
            $table->dropColumn('start_year_date');
            $table->dropColumn('finish_year_date');
        });
    }
}