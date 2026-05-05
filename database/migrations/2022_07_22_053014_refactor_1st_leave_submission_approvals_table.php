<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stLeaveSubmissionApprovalsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submission_approvals', function (Blueprint $table) {
			$table->dropColumn('id_department');
			$table->dropColumn('position_level')->nullable();
			$table->bigInteger('id_approver_position')->after('id_leave_submission');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leave_submission_approvals', function (Blueprint $table) {
			$table->bigInteger('id_department')->after('id_leave_submission');
			$table->integer('position_level')->after('id_department');
			$table->dropColumn('id_approver_position');
		});
	}
}
