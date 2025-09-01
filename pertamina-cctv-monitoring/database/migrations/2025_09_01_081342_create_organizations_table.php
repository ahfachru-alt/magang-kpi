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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color', 7)->default('#3B82F6');
            $table->string('secondary_color', 7)->default('#1F2937');
            $table->json('settings')->nullable(); // Custom settings per organization
            $table->json('features')->nullable(); // Feature flags per organization
            $table->enum('plan', ['basic', 'professional', 'enterprise'])->default('basic');
            $table->integer('max_users')->default(10);
            $table->integer('max_cctvs')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['slug', 'is_active']);
            $table->index(['domain', 'is_active']);
            $table->index(['plan', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
