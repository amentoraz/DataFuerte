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


        Schema::create('element_types', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->string('name'); // String "name"
            $table->timestamps(); // created_at and updated_at columns
        });


        // Keys are shorter
        Schema::create('elements', function (Blueprint $table) {            
            $table->uuid('uuid')->unique()->primary(); // UUID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->uuid('parent'); // 0 if root, otherwise the id of the parent element
            $table->foreignId('element_type_id')->constrained()->onDelete('cascade'); // Foreign key to element_types table
            $table->string('key'); // String "key"
            $table->binary('content'); // BLOB "content"
            $table->binary('iv'); // BLOB "iv"
            $table->binary('salt'); // BLOB "salt"
            $table->string('hmac'); // String "hmac"
            $table->integer('iterations'); // Integer "iterations"
            $table->timestamps(); // created_at and updated_at columns
        });





    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
        Schema::dropIfExists('element_types');
    }
};