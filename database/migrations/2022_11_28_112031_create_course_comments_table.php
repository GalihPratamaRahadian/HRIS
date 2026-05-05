<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseCommentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_comments', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course')->nullable();
			$table->bigInteger('id_employee')->nullable();
			$table->bigInteger('id_user')->nullable();
			$table->text('comment')->nullable();
			$table->bigInteger('id_comment_reply')->nullable();
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
		Schema::dropIfExists('course_comments');
	}
}
