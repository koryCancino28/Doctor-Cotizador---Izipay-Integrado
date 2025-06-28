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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade'); 
            $table->decimal('total', 8, 2); 
            $table->string('pdf_filename')->nullable();
            $table->text('observacion')->nullable();
            $table->enum('tipo_pago', ['contra_entrega', 'pasarela_izipay', 'transferencia'])->default('contra_entrega');
            $table->string('voucher_path')->nullable();
            $table->string('codigo_transaccion')->nullable()->comment('Código de transacción o depósito');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};