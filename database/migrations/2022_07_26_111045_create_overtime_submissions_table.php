<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvertimeSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('overtime_submissions', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->date('start_date');
			$table->date('end_date');
			$table->time('clock_start');
			$table->time('clock_end');
			$table->bigInteger('id_overtime_reason');
			$table->string('status')->nullable();
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();
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
		Schema::dropIfExists('overtime_submissions');
	}
}
