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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('adresse');
            $table->string('telephone')->nullable();
            $table->string('password')->hash();
            $table->string('fonction');
            $table->string('email')->unique();
            $table->string('photo')->nullable();
            $table->enum('statut', ['Actif', 'Bloquer'])->default('Actif');
            $table->enum('role', ['Admin', 'Manager', 'CM','Coach','Apprenant']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
