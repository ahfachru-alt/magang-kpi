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
        Schema::create('cctv_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cctv_id')->constrained()->onDelete('cascade');
            $table->string('type'); // response_time_anomaly, status_pattern_anomaly, usage_pattern_anomaly, geographic_anomaly
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->text('description');
            $table->json('data'); // Additional anomaly data
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['cctv_id', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['type', 'detected_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctv_anomalies');
    }
};
