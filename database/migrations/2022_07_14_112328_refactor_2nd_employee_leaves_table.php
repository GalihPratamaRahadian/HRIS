<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndEmployeeLeavesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->string('reason')->nullable()->after('id_employee');
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
			$table->dropColumn('reason');
		});
	}
}
