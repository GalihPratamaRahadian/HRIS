<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add3rdColumnsAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->text('meta')->nullable()->after('clock_out_at');
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
			$table->dropColumn('meta');
		});
	}
}
