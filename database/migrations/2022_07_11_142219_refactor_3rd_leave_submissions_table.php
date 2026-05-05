<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor3rdLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->bigInteger('id_leave_reason')->after('id_employee');
			$table->dropColumn('meta');
			$table->dropColumn('leave_reason');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->dropColumn('id_leave_reason');
			$table->text('meta')->nullable()->after('id_employee_leave');
			$table->text('leave_reason')->nullable()->after('id_leave_submission');
		});
	}
}
