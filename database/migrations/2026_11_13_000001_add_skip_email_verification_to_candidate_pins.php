<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tb_cand_pin', 'skip_email_verification')) {
            Schema::table('tb_cand_pin', function (Blueprint $table) {
                $table->boolean('skip_email_verification')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_cand_pin', 'skip_email_verification')) {
            Schema::table('tb_cand_pin', function (Blueprint $table) {
                $table->dropColumn('skip_email_verification');
            });
        }
    }
};
