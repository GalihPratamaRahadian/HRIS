<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courses', function (Blueprint $table) {
			$table->id();
			$table->string('course_title')->nullable();
			$table->bigInteger('id_department')->nullable();
			$table->bigInteger('id_employee_group')->nullable();
			$table->string('video_source')->nullable();
			$table->string('video_link')->nullable();
			$table->string('filename')->nullable();
			$table->string('is_published')->default('yes')->nullable();
			$table->softDeletes();
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
		Schema::dropIfExists('courses');
	}
}
