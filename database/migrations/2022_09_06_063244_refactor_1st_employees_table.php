<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stEmployeesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employees', function (Blueprint $table) {
			$table->string('shift_type')->after('id_position')->nullable()->default('routine');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employees', function (Blueprint $table) {
			$table->dropColumn('shift_type');
		});
	}
}
