<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stOvertimeSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->integer('approval_progress_level')->after('id_overtime_reason')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->dropColumn('approval_progress_level');
        });
    }
}
