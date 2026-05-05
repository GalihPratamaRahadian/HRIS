<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingParticipantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('training_participants', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_training')->nullable();
			$table->bigInteger('id_employee')->nullable();
			$table->string('status')->default('Mengikuti')->nullable();
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
		Schema::dropIfExists('training_participants');
	}
}
