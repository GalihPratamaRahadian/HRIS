<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor5thLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->timestamp('canceled_at')->after('rejected_at')->nullable();
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
			$table->dropColumn('canceled_at');
		});
	}
}
