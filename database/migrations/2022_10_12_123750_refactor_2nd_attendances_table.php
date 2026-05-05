<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->bigInteger('id_employee_leave')->nullable()->after('clock_out_at');
			$table->bigInteger('id_off_day')->nullable()->after('id_employee_leave');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->dropColumn('id_employee_leave');
			$table->dropColumn('id_off_day');
		});
	}
}
