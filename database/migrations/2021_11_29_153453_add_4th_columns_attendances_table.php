<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add4thColumnsAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->bigInteger('id_shift')->nullable()->after('id_employee');
			$table->bigInteger('late_tolerance')->default(0)->after('shift_clock_out');
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
			$table->dropColumn('id_shift');
			$table->dropColumn('late_tolerance');
		});
	}
}
