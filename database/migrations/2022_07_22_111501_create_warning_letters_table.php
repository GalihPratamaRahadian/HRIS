<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarningLettersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('warning_letters', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('type');
			$table->date('start_date');
			$table->date('end_date');
			$table->longText('message')->nullable();
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
		Schema::dropIfExists('warning_letters');
	}
}
