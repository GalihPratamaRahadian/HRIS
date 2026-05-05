<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreVisitsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_visits', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee')->nullable();
			$table->bigInteger('id_store')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->string('photo')->nullable();
			$table->timestamp('visited_at')->nullable();
			$table->string('purchase')->nullable();
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
		Schema::dropIfExists('store_visits');
	}
}
