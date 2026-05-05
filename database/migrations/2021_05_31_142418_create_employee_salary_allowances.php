<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalaryAllowances extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_salary_allowances', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee_salary');
			$table->string('allowance_name');
			$table->bigInteger('allowance_nominal')->default(0);
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
		Schema::dropIfExists('employee_salary_allowances');
	}
}
