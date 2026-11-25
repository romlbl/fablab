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
            $table->foreignId('id_type')->constrained('type_jobs')->restrictOnDelete();
            $table->string('status');
            $table->unsignedBigInteger('id_operateur')->nullable();
            $table->unsignedBigInteger('id_client')->nullable();
            $table->unsignedBigInteger('id_projet')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('debut')->nullable();
            $table->timestamp('fin')->nullable();
            $table->unsignedBigInteger('id_machine')->nullable();
            $table->foreignId('id_type_travail')->nullable()->constrained('type_travails')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
