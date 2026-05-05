<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndLeaveSubmissionApprovalsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submission_approvals', function (Blueprint $table) {
			$table->integer('level')->default(1)->nullable()->after('id_leave_submission');
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
			$table->dropColumn('level');
		});
	}
}
