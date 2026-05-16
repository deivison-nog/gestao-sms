<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->date('month');
            $table->unsignedTinyInteger('worked_days')->default(0);
            $table->unsignedTinyInteger('justified_absences')->default(0);
            $table->unsignedTinyInteger('unjustified_absences')->default(0);
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->unique(['professional_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
