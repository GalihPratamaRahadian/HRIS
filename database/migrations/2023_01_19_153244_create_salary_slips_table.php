<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySlipsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_slips', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->integer('month')->nullable();
			$table->string('month_name')->nullable();
			$table->integer('year')->nullable();
			$table->string('filename')->nullable();
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
		Schema::dropIfExists('salary_slips');
	}
}
