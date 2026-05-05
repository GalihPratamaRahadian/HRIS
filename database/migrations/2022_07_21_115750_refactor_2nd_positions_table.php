<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndPositionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('positions', function (Blueprint $table) {
			$table->longText('performance_goals')->nullable()->after('job_description');
			$table->bigInteger('approver_1')->nullable()->after('performance_goals');
			$table->bigInteger('approver_2')->nullable()->after('approver_1');
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
			$table->dropColumn('performance_goals');
			$table->dropColumn('approver_1');
			$table->dropColumn('approver_2');
		});
	}
}
