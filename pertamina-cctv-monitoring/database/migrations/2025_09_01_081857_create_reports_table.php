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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // dashboard, chart, table, export
            $table->json('configuration'); // Report configuration and layout
            $table->json('filters')->nullable(); // Report filters
            $table->json('data_source'); // Data source configuration
            $table->enum('refresh_interval', ['manual', '5min', '15min', '1hour', '1day'])->default('manual');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_cron')->nullable(); // Cron expression for scheduling
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamp('next_generation_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'error'])->default('active');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['organization_id', 'type']);
            $table->index(['organization_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['is_scheduled', 'next_generation_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
