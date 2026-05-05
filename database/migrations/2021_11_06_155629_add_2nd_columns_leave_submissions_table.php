<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2ndColumnsLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->text('meta')->nullable()->after('id_employee_leave');
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
			$table->dropColumn('meta');
		});
	}
}
