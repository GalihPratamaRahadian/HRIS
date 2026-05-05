<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor4thPositionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('positions', function (Blueprint $table) {
			$table->dropColumn('position_level');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('positions', function (Blueprint $table) {
			$table->tinyInteger('position_level')->default(1)->after('job_description');
		});
	}
}
