<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1stRefactorAnnouncementsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('announcements', function (Blueprint $table) {
			$table->bigInteger('id_department')->after('title')->nullable();
			$table->bigInteger('id_employee_group')->after('id_department')->nullable();
			$table->string('is_published')->after('id_employee_group')->nullable('yes');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('announcements', function (Blueprint $table) {
			$table->dropColumn('id_department');
			$table->dropColumn('id_employee_group');
			$table->dropColumn('is_published');
		});
	}
}
