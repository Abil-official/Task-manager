<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_executors', function (Blueprint $table) {
            $table->foreignUuid('executor_id');
            $table->foreignUuid('task_id');
            $table->string('status')->default('pending');

            $table->primary(['task_id', 'executor_id']);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_executors');
    }
};
