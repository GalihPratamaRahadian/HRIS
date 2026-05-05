<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stLeaveReasonsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leave_reasons', function (Blueprint $table) {
			$table->dropColumn('is_absolute_duration');
			$table->string('leave_type')->nullable()->after('max_duration');
			$table->string('is_using_max_duration')->default('no')->nullable()->after('leave_type');
			$table->string('is_cut_leave_quota')->default('no')->nullable()->after('is_using_max_duration');
			$table->string('is_required_file')->default('no')->nullable()->after('is_cut_leave_quota');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leave_reasons', function (Blueprint $table) {
			$table->string('is_absolute_duration')->default('no')->nullable()->after('max_duration');
			$table->dropColumn('leave_type');
			$table->dropColumn('is_using_max_duration');
			$table->dropColumn('is_cut_leave_quota');
			$table->dropColumn('is_required_file');
		});
	}
}
