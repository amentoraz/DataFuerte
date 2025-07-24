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

        // FOlders
        Schema::create('folders', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade'); // Foreign key to folders table
            $table->string('name'); // String "name"
            $table->timestamps(); // created_at and updated_at columns
        });

        // Keys are shorter
        Schema::create('passwords', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('folder_id')->constrained()->onDelete('cascade'); // Foreign key to folders table
            $table->string('key'); // String "key"
            $table->binary('content'); // BLOB "content"
            $table->binary('iv'); // BLOB "iv"
            $table->binary('salt'); // BLOB "salt"
            $table->string('hmac'); // String "hmac"
            $table->timestamps(); // created_at and updated_at columns
        });

        // Texts are longer
        Schema::create('texts', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('folder_id')->constrained()->onDelete('cascade'); // Foreign key to folders table
            $table->string('key'); // String "key"
            $table->binary('content'); // BLOB "content"
            $table->binary('iv'); // BLOB "iv"
            $table->binary('salt'); // BLOB "salt"
            $table->string('hmac'); // String "hmac"            
            $table->timestamps(); // created_at and updated_at columns
        });

        // Files 
        Schema::create('files', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('folder_id')->constrained()->onDelete('cascade'); // Foreign key to folders table
            $table->string('key'); // String "key"
            $table->binary('content'); // BLOB "content"
            $table->binary('iv'); // BLOB "iv"
            $table->binary('salt'); // BLOB "salt"
            $table->string('hmac'); // String "hmac"            
            $table->timestamps(); // created_at and updated_at columns
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passwords');
        Schema::dropIfExists('texts');
    }
};