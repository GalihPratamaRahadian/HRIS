<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stEmployeeTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_trainings', function (Blueprint $table) {
            $table->string('provider')->after('date_end')->nullable();
            $table->string('file')->after('provider')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_trainings', function (Blueprint $table) {
            $table->dropColumn('provider');
            $table->dropColumn('file');
        });
    }
}
