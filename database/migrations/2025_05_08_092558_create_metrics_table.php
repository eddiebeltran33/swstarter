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
        Schema::create('metrics', function (Blueprint $table) {
            $table->comment('Table to store aggregated metrics from the request_stats table');
            $table->id();
            $table->string('name')->comment('Name of the aggregated metric');
            $table->json('metadata')->nullable()->comment('JSON value of the metric');
            $table->json('value')->nullable()->comment('JSON value of the metric');
            $table->timestamp('start_at')->comment('The beginning of the time interval for aggregating query stats');
            $table->timestamp('end_at')->comment('The end of the time interval for aggregating query stats');
            $table->index(['name', 'start_at', 'end_at'], 'idx_metrics_name_start_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
