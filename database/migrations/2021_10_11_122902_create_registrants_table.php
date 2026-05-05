<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('registrants', function (Blueprint $table) {
			$table->id();
			$table->string('employee_number')->nullable();
			$table->string('employee_name');
			$table->string('gender', 1)->nullable();
			$table->string('email')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('jamsostek')->nullable();
			$table->string('job_status')->nullable();
			$table->text('photo')->nullable();
			$table->bigInteger('id_department')->nullable();
			$table->bigInteger('id_position')->nullable();
			$table->bigInteger('id_shift')->nullable();
			$table->bigInteger('id_user')->nullable();
			$table->bigInteger('id_employee')->nullable();
			$table->tinyInteger('registration_status')->default(1);
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();
			$table->timestamp('edited_at')->nullable();
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
		Schema::dropIfExists('registrants');
	}
}
