<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); 
            $table->string('nom'); 
            $table->unsignedBigInteger('id_type_client');
            $table->string('code_acces_hash')->unique(); 
            $table->timestamp('date_creation')->useCurrent(); 
            $table->timestamp('date_archive')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
