<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2ndColumnsEmployeeLeavesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->text('meta')->nullable()->after('file');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->dropColumn('meta');
		});
	}
}
