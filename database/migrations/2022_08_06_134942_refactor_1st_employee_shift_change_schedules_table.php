<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stEmployeeShiftChangeSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_change_schedules', function (Blueprint $table) {
            $table->timestamp('change_at')->after('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_shift_change_schedules', function (Blueprint $table) {
            $table->dropColumn('change_at');
        });
    }
}
