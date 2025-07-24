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
        // Las claves son más cortas
        Schema::create('passwords', function (Blueprint $table) {
            $table->id(); // Columna para el ID autoincremental
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clave foránea a la tabla users
            $table->string('key'); // Columna para el string "key"
            $table->binary('content'); // Columna para el BLOB "content"
            $table->binary('iv'); // Columna para el BLOB "iv"
            $table->binary('salt'); // Columna para el BLOB "salt"
            $table->string('hmac'); // Columna para el string "hmac"
            $table->timestamps(); // Columnas created_at y updated_at
        });

        // Los textos son más largos
        Schema::create('texts', function (Blueprint $table) {
            $table->id(); // Columna para el ID autoincremental
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clave foránea a la tabla users
            $table->string('key'); // Columna para el string "key"
            $table->binary('content'); // Columna para el BLOB "content"
            $table->binary('iv'); // Columna para el BLOB "iv"
            $table->binary('salt'); // Columna para el BLOB "salt"
            $table->string('hmac'); // Columna para el string "hmac"            
            $table->timestamps(); // Columnas created_at y updated_at
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