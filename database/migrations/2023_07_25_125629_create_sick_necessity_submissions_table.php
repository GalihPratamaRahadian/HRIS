<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSickNecessitySubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sick_necessity_submissions', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee')->nullable();
			$table->string('type')->nullable();
			$table->bigInteger('id_sick_reason')->nullable();
			$table->bigInteger('id_necessity_reason')->nullable();
			$table->string('reason')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->text('description')->nullable();
			$table->string('file')->nullable();
			$table->integer('approval_progress_level')->default(1)->nullable();
			$table->string('status')->nullable();
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();
			$table->timestamp('canceled_at')->nullable();
			$table->bigInteger('id_employee_sick_necessity')->nullable();
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
		Schema::dropIfExists('sick_necessity_submissions');
	}
}
