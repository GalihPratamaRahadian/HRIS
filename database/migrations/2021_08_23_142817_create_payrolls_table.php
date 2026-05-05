<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payrolls', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->date('period_start');
			$table->date('period_end');
			$table->bigInteger('basic_salary')->default(0);
			$table->bigInteger('total_allowance')->default(0);
			$table->bigInteger('total_cut')->default(0);
			$table->bigInteger('bonus')->default(0);
			$table->bigInteger('total')->default(0);
			$table->text('notes')->nullable();
			$table->date('publish_date');
			$table->tinyInteger('publish_status')->default(1);
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
		Schema::dropIfExists('payrolls');
	}
}
