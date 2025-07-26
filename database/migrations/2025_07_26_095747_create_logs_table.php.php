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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');  // Nullable because it can be an anonymous user
            $table->string('action'); // get, create, update, delete
            $table->string('loggable_type')->nullable(); // For polymorphism: affected model type (e.g. 'App\Models\Item')
            $table->string('loggable_id')->nullable(); // String since it could be a UUID
            $table->json('data')->nullable(); // Additional data in JSON format (very flexible)
            $table->string('ip_address', 45)->nullable(); // User's IP address
            $table->text('user_agent')->nullable(); // User Agent of the browser
            $table->string('severity')->default('INFO');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
