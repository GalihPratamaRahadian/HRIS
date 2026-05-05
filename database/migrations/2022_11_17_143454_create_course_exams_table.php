<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_exams', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course');
			$table->integer('duration')->default(0)->nullable();
			$table->string('is_random_question')->default('no')->nullable();
			$table->integer('amount_of_questions')->default(0)->nullable();
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
		Schema::dropIfExists('course_exams');
	}
}
