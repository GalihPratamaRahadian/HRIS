<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceTerminalUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('face_terminal_users', function (Blueprint $table) {
			$table->id();
			$table->string('type');
			$table->bigInteger('id_reference');
			$table->string('employee_number')->nullable();
			$table->string('card_number')->nullable();
			$table->string('finger')->nullable();
			$table->date('valid_start');
			$table->date('valid_end');
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
		Schema::dropIfExists('face_terminal_users');
	}
}
