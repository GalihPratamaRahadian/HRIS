<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stColumnsLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->timestamp('approved_at')->nullable()->after('status');
			$table->timestamp('rejected_at')->nullable()->after('approved_at');
			$table->bigInteger('id_employee_leave')->nullable()->after('rejected_at');
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
			$table->dropColumn('approved_at');
			$table->dropColumn('rejected_at');
			$table->dropColumn('id_employee_leave');
		});
	}
}
