<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('type_travails', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('id_type_jobs')->constrained('type_jobs')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['nom', 'id_type_jobs']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('type_travails');
    }
};
