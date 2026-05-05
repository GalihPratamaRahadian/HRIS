<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoClockToTrainingParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_participants', function (Blueprint $table) {
            $table->string('photo_clock')->nullable()->after('photo');
            $table->date('photo_date')->nullable()->after('photo_clock');
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
            $table->dropColumn('photo_clock');
            $table->dropColumn('photo_date');
        });
    }
}
