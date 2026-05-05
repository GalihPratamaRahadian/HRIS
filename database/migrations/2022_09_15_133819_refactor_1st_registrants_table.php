<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stRegistrantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('registrants', function (Blueprint $table) {
			$table->string('shift_type')->after('id_position')->nullable();
			$table->bigInteger('id_employee_group')->after('id_user')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('registrants', function (Blueprint $table) {
			$table->dropColumn('shift_type');
			$table->dropColumn('id_employee_group');
		});
	}
}
