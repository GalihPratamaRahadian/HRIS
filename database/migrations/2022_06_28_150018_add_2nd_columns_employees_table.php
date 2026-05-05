<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2ndColumnsEmployeesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employees', function (Blueprint $table) {
			$table->date('start_working_date')->after('id_user')->nullable();
			$table->string('place_of_birth')->after('start_working_date')->nullable();
			$table->date('date_of_birth')->after('place_of_birth')->nullable();
			$table->text('address')->after('date_of_birth')->nullable();
			$table->string('last_education')->after('address')->nullable();
			$table->string('last_education_major')->after('last_education')->nullable();
			$table->string('marital_status')->after('last_education_major')->nullable();
			$table->string('blood_type')->after('marital_status')->nullable();
			$table->string('ktp_number')->after('blood_type')->nullable();
			$table->string('npwp_number')->after('ktp_number')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employees', function (Blueprint $table) {
			$table->dropColumn('start_working_date');
			$table->dropColumn('place_of_birth');
			$table->dropColumn('date_of_birth');
			$table->dropColumn('address');
			$table->dropColumn('last_education');
			$table->dropColumn('last_education_major');
			$table->dropColumn('marital_status');
			$table->dropColumn('blood_type');
			$table->dropColumn('ktp_number');
			$table->dropColumn('npwp_number');
		});
	}
}
