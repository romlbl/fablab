<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->text('chemin_image')->nullable();
            $table->foreignId('id_client')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('id_categorie_projet')->nullable()->constrained('categories_projets')->nullOnDelete();
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_archive')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
