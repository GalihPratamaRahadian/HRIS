<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor4thLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_submissions', function (Blueprint $table) {
			$table->bigInteger('approval_progress_level')->default(1)->nullable()->after('file');
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
			$table->dropColumn('approval_progress_level');
		});
	}
}
