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
        Schema::create('weekly_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('minggu_ke');
            $table->decimal('berat_kg', 5, 2);
            $table->decimal('pinggang_cm', 5, 2)->nullable();
            $table->unsignedTinyInteger('energi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'minggu_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_checkins');
    }
};
