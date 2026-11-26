<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_categorie')->constrained('categories')->restrictOnDelete();
            $table->string('status')->index();
            $table->foreignId('id_operateur')->nullable()->constrained('collaborateurs')->restrictOnDelete();
            $table->foreignId('id_client')->nullable()->constrained('clients')->restrictOnDelete();
            $table->foreignId('id_projet')->nullable()->constrained('projets')->restrictOnDelete();
            $table->foreignId('id_machine')->nullable()->constrained('machines')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->timestamp('debut')->nullable();
            $table->timestamp('fin')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
