<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeEducationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_educations', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('education_level');
			$table->string('school_name');
			$table->string('major_name');
			$table->integer('year_start');
			$table->integer('year_end');
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
		Schema::dropIfExists('employee_educations');
	}
}
