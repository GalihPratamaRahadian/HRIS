<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stShiftsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('shifts', function (Blueprint $table) {
			$table->time('clock_start_limit')->nullable()->after('shift_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('shifts', function (Blueprint $table) {
			$table->dropColumn('clock_start_limit');
		});
	}
}
