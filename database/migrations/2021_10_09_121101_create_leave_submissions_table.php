<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_submissions', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->tinyInteger('leave_reason')->default(1);
			$table->date('start_date');
			$table->date('end_date');
			$table->text('description')->nullable();
			$table->string('file')->nullable();
			$table->tinyInteger('status')->default(1);
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
		Schema::dropIfExists('leave_submissions');
	}
}
