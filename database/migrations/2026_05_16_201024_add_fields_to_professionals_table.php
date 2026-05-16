<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('name');
            $table->string('cpf', 14)->nullable()->unique()->after('birth_date');
            $table->string('class_registry')->nullable()->after('cpf');
            $table->foreignId('position_id')->nullable()->after('class_registry')->constrained('positions')->nullOnDelete();
            $table->string('workload')->nullable()->after('position_id');
            $table->enum('contract_type', ['efetivo', 'temporario', 'estagio'])->nullable()->after('workload');
            $table->string('position')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
            $table->dropColumn(['birth_date', 'cpf', 'class_registry', 'workload', 'contract_type']);
            $table->string('position')->nullable(false)->change();
        });
    }
};
