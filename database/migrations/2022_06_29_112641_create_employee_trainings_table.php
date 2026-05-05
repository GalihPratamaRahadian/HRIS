<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTrainingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_trainings', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('training_name');
			$table->date('date_start');
			$table->date('date_end');
			$table->text('description')->nullable();
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
		Schema::dropIfExists('employee_trainings');
	}
}
