<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendances', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->date('date');
			$table->time('clock_in')->nullable();
			$table->time('clock_out')->nullable();
			$table->string('clock_in_method')->nullable();
			$table->string('clock_out_method')->nullable();
			$table->tinyInteger('type')->default(1);
			$table->text('description')->nullable();
			$table->integer('late')->default(0);
			$table->integer('overtime')->default(0);
			$table->tinyInteger('is_overtime')->default(1);
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
		Schema::dropIfExists('attendances');
	}
}
