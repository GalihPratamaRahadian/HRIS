<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalaries extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_salaries', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->bigInteger('basic_salary');
			$table->bigInteger('overtime_pay');
			$table->bigInteger('total_allowance')->default(0);
			$table->bigInteger('total_cut')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('employee_salaries');
	}
}
