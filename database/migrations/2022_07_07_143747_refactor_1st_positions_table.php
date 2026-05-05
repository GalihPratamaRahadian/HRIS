<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stPositionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('positions', function (Blueprint $table) {
			$table->bigInteger('id_department')->after('position_name');
			$table->longText('job_description')->nullable()->after('id_department');
			$table->tinyInteger('position_level')->default(1)->after('job_description');
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
			$table->dropColumn('id_department');
			$table->dropColumn('job_description');
			$table->dropColumn('position_level');
		});
	}
}
