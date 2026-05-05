<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor3rdEmployeeLeavesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->bigInteger('id_leave_reason')->nullable()->after('id_employee');
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
			$table->dropColumn('id_leave_reason');
		});
	}
}
