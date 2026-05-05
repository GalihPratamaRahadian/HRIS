<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stColumnsAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->timestamp('shift_clock_in')->nullable()->after('date');
			$table->timestamp('shift_clock_out')->nullable()->after('shift_clock_in');
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
			$table->dropColumn('shift_clock_in');
			$table->dropColumn('shift_clock_out');
		});
	}
}
