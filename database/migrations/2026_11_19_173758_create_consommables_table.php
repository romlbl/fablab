<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('consommables', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('chemin_image')->nullable();
            $table->foreignId('id_categorie')->constrained('categories')->restrictOnDelete();
            $table->foreignId('id_unite')->constrained('unites')->restrictOnDelete();
            $table->string('fournisseur')->nullable();
            $table->decimal('prix_unite', 10, 2)->nullable();
            $table->string('format_brute_vente')->nullable();
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_archive')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consommables');
    }
};
