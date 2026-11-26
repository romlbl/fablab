<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('jobs_consommables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_job')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('id_consommable')->constrained('consommables')->restrictOnDelete();
            $table->decimal('cout_tot', 10, 2);
            $table->decimal('quantite', 10, 2);
            $table->decimal('cout', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs_consommables');
    }
};
