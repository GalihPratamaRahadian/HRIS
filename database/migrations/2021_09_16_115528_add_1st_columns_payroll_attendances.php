<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stColumnsPayrollAttendances extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payroll_attendances', function (Blueprint $table) {
			$table->bigInteger('salary')->default(0)->after('id_attendance');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payroll_attendances', function (Blueprint $table) {
			$table->dropColumn('salary');
		});
	}
}
