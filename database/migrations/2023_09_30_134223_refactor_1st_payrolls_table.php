<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stPayrollsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payrolls', function (Blueprint $table) {
			$table->string('send_status')->after('publish_status')->nullable();
			$table->timestamp('send_schedule')->after('send_status')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payrolls', function (Blueprint $table) {
			$table->dropColumn('send_status');
			$table->dropColumn('send_schedule');
		});
	}
}
