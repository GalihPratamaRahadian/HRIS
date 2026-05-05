<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stColumnsEmployeesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employees', function (Blueprint $table) {
			$table->bigInteger('id_user')->after('status')->nullable();
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
			$table->dropColumn('id_user');
		});
	}
}
