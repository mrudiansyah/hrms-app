<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password_copy')) {
                $table->string('password_copy')->nullable();
            }

            if (!Schema::hasColumn('users', 'maintenance')) {
                $table->boolean('maintenance')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'password_copy')) {
                $columns[] = 'password_copy';
            }

            if (Schema::hasColumn('users', 'maintenance')) {
                $columns[] = 'maintenance';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
