<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingMaterialsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('training_materials', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_training')->nullable();
			$table->string('title')->nullable();
			$table->string('material_type')->nullable();
			$table->string('file_material')->nullable();
			$table->string('link_youtube')->nullable();
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
		Schema::dropIfExists('training_materials');
	}
}
