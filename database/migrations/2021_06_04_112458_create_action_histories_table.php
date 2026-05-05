<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionHistoriesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_histories', function (Blueprint $table) {
			$table->id();
			$table->string('date');
			$table->string('action');
			$table->bigInteger('id_reference')->nullable();
			$table->string('reference')->nullable();
			$table->text('description')->nullable();
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
		Schema::dropIfExists('action_histories');
	}
}
