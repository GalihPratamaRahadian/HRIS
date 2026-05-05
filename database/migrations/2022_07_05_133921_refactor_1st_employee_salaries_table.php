<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stEmployeeSalariesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_salaries', function (Blueprint $table) {
			$table->bigInteger('daily_meal_allowance')->after('overtime_pay')->default(0);
			$table->bigInteger('daily_transportation_allowance')->after('daily_meal_allowance')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employee_salaries', function (Blueprint $table) {
			$table->dropColumn('daily_meal_allowance');
			$table->dropColumn('daily_transportation_allowance');
		});
	}
}
