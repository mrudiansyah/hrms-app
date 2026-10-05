<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackupTrainerToTrainingSchedule extends Migration
{
    public function up()
    {
        Schema::table('tb_training_schedule', function (Blueprint $table) {
            $table->string('nara_sumber_backup')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tb_training_schedule', function (Blueprint $table) {
            $table->dropColumn('nara_sumber_backup');
        });
    }
}
