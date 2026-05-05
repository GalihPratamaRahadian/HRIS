<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2ndColumnsAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->timestamp('clock_in_at')->after('is_overtime')->nullable();
			$table->timestamp('clock_out_at')->after('clock_in_at')->nullable();
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
			$table->dropColumn('clock_in_at');
			$table->dropColumn('clock_out_at');
		});
	}
}
