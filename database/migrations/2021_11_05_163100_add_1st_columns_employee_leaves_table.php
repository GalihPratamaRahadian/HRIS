<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stColumnsEmployeeLeavesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->string('file')->nullable()->after('description');
			$table->dropColumn('photo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employee_leaves', function (Blueprint $table) {
			$table->string('photo')->nullable()->after('description');
			$table->dropColumn('file');
		});
	}
}
