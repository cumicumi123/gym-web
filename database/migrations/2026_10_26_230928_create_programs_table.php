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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('level');
            $table->string('goal');
            $table->unsignedSmallInteger('usia');
            $table->decimal('berat_kg', 5, 2);
            $table->decimal('tinggi_cm', 5, 2);
            $table->unsignedSmallInteger('pengalaman_bulan');
            $table->string('gender')->nullable();
            $table->string('split')->nullable();
            $table->unsignedSmallInteger('kalori');
            $table->unsignedSmallInteger('protein_g');
            $table->unsignedSmallInteger('lemak_g');
            $table->unsignedSmallInteger('karbo_g');
            $table->unsignedSmallInteger('air_ml');
            $table->unsignedTinyInteger('sesi_per_minggu');
            $table->json('schedule_days')->nullable();
            $table->date('mulai_pada');
            $table->boolean('aktif')->default(true);
            $table->timestamp('consent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
