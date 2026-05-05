<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndCourseParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_participants', function (Blueprint $table) {
            $table->string('exam_passed')->default('no')->nullable()->after('video_duration');
            $table->timestamp('exam_passed_at')->nullable()->after('exam_passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_participants', function (Blueprint $table) {
            $table->dropColumn('exam_passed');
            $table->dropColumn('exam_passed_at');
        });
    }
}
