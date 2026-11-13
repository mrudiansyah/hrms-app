<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('tb_cand_employees', 'UX_tb_cand_employees_email_address')) {
            Schema::table('tb_cand_employees', function (Blueprint $table) {
                $table->unique('email_address', 'UX_tb_cand_employees_email_address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('tb_cand_employees', 'UX_tb_cand_employees_email_address')) {
            Schema::table('tb_cand_employees', function (Blueprint $table) {
                $table->dropUnique('UX_tb_cand_employees_email_address');
            });
        }
    }
};
