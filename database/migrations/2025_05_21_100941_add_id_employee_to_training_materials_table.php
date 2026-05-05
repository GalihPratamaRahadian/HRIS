<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdEmployeeToTrainingMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->bigInteger('id_employee')->nullable()->after('id_training');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->dropColumn('id_employee');
        });
    }
}
