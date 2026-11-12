<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_table_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 255)->unique();
            $table->json('allowed_columns');
            $table->string('row_scope', 20);
            $table->string('scope_column', 128)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_table_permissions');
    }
};
