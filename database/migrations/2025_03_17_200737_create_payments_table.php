<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // Código de respuesta
            $table->string('message', 255); // Mensaje de la operación
            $table->string('pay_method', 50)->nullable(); // Método de pago (ej: CARD)
            $table->decimal('amount', 10, 2)->nullable(); // Monto
            $table->string('currency', 10)->nullable(); // Moneda (PEN)
            $table->string('order_number', 50)->nullable(); // Número de orden
            $table->string('authorization_code', 50)->nullable(); // Código de autorización
            $table->string('transaction_id', 100)->nullable(); // Id de transacción
            $table->string('state_message', 100)->nullable(); // Estado de la transacción 
            $table->date('transaction_date')->nullable(); // Fecha de transacción
            $table->time('transaction_time')->nullable(); // Hora de transacción
            $table->string('unique_id', 100)->nullable(); // ID único de la transacción
            $table->string('reference_number', 100)->nullable(); // Número de referencia
            $table->string('merchant_code', 50)->nullable(); // Código de comercio
            $table->string('buyer_id', 100)->nullable(); // ID del comprador (token.merchantBuyerId)

            // Información de tarjeta
            $table->string('card_brand', 50)->nullable(); // Marca de tarjeta 
            $table->string('card_pan_masked', 50)->nullable(); // Número de tarjeta enmascarado

            // Datos del cliente (billing)
            $table->string('customer_first_name', 100)->nullable();
            $table->string('customer_last_name', 100)->nullable();
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_document_type', 10)->nullable();
            $table->string('customer_document', 20)->nullable();
            $table->string('customer_address', 255)->nullable();
            $table->string('customer_city', 100)->nullable();
            $table->string('customer_country', 5)->nullable();

            $table->json('full_response'); //JSON completo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
