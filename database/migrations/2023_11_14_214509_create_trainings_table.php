<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trainings', function (Blueprint $table) {
			$table->id();
			$table->string('title')->nullable();
			$table->string('trainer_name')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->string('is_published')->nullable()->default('Yes');
			$table->string('id_department')->nullable();
			$table->string('id_position')->nullable();
			$table->string('id_employee_group')->nullable();
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
		Schema::dropIfExists('trainings');
	}
}
