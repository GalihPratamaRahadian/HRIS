<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToTrainingParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_participants', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('id_employee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_participants', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
}
