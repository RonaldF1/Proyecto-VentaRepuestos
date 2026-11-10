<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tienda');
            $table->string('sku', 100);
            $table->string('nombre', 200);
            $table->string('tipo'); // simple, variable, agrupado, externo
            $table->integer('precio')->nullable();
            $table->integer('existencias')->nullable();
            $table->json('categorias');
            $table->string('campo_propio_nombre')->default('numero_parte_oem');
            $table->string('campo_propio_valor')->nullable();
            $table->timestamp('sincronizado_en')->nullable();
            $table->timestamps();

            $table->index('sku');
            $table->index('id_tienda');
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // ID de WooCommerce o local
            $table->string('estado');
            $table->string('moneda', 10)->default('CRC');
            $table->integer('total');
            $table->timestamp('creado_en');
            $table->json('lineas');
            $table->string('direccion_estado')->default('sin_procesar'); // sin_procesar, normalizada, pendiente_revision
            $table->text('direccion_texto');
            $table->json('direccion_normalizada')->nullable();
            $table->string('conector_estado')->default('pendiente'); // pendiente, exito, fallo
            $table->string('conector_detalle')->nullable();
            $table->string('conector_referencia_externa')->nullable();
            $table->timestamp('conector_actualizado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->string('direccion'); // entrante, saliente
            $table->string('sistema'); // tienda, conector, ia, heredado
            $table->string('operacion');
            $table->string('estado'); // exito, fallo, duplicado, firma_invalida
            $table->integer('codigo_http')->nullable();
            $table->integer('intentos')->default(1);
            $table->integer('duracion_ms')->default(0);
            $table->string('correlacion')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('sistema');
            $table->index('estado');
            $table->index('direccion');
            $table->index('fecha');
            $table->index('correlacion');
        });

        Schema::create('webhook_entregas', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_entregas');
        Schema::dropIfExists('bitacoras');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('productos');
    }
};
