<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFamiliesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_families', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('name');
			$table->string('relationship_status');
			$table->string('place_of_birth');
			$table->date('date_of_birth');
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
		Schema::dropIfExists('employee_families');
	}
}
