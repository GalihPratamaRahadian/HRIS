<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalaryCuts extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_salary_cuts', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee_salary');
			$table->string('cut_name');
			$table->bigInteger('cut_nominal')->default(0);
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
		Schema::dropIfExists('employee_salary_cuts');
	}
}
