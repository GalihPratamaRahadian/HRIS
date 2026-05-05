<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyShiftResumesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('daily_shift_resumes', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee')->nullable();
			$table->string('type')->default('Normal')->nullable();
			$table->date('date')->nullable();
			$table->timestamp('clock_start_at')->nullable();
			$table->timestamp('clock_end_at')->nullable();
			$table->integer('late_tolerance')->default(0)->nullable();
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
		Schema::dropIfExists('daily_shift_resumes');
	}
}
