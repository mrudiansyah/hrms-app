<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_training_assignment', function (Blueprint $table) {
            $table->string('assignment_file', 512)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tb_training_assignment', function (Blueprint $table) {
            $table->dropColumn('assignment_file');
        });
    }
};
