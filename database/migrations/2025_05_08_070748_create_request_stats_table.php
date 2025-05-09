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
        Schema::create('request_stats', function (Blueprint $table) {
            $table->comment('Table to store individual request stats for later aggregation');
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('started_at', 6)->comment('The timestamp when the request started measured by the server')
                ->index();
            $table->timestamp('ended_at', 6)->comment('The timestamp when the request ended measured by the server');
            $table->string('search_term')->nullable()->comment('Search term used in the request if any');
            $table->string('resource_id')->nullable()->comment('Resource ID from the request if any');
            $table->integer('duration')->comment('Duration in milliseconds');
            $table->json('metadata')->nullable()->comment('Aditional metadata about the request');
            // Additional fields to match the middleware
            $table->string('action')->nullable()->comment('Controller@method called for the request');
            $table->enum('outcome', ['success', 'failure'])->nullable()->comment('Outcome of the request');
            $table->enum('http_request_method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'])->nullable();
            $table->string('client_ip')->nullable();
            $table->unsignedInteger('http_status_code')->nullable()->comment('HTTP status code of the response');
            $table->text('url')->nullable()->comment('URL of the request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_stats');
    }
};
