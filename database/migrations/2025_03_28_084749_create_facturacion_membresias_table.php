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
        Schema::create('facturacion_membresias', function (Blueprint $table) {
            $table->id('id_factura_membresia'); // ID autoincremental
            $table->unsignedBigInteger('id_cliente_fx');
            $table->unsignedBigInteger('id_establecimiento_fx');
            $table->unsignedBigInteger('id_aplicacion_web');
            $table->unsignedBigInteger('id_estado');
            $table->string('monto_total')->default(0);
            $table->string('medio_pago')->default('NA');
            $table->integer('dias_restantes')->nullable();
        
            $table->timestamp('fecha_pago')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_cliente_fx')->references('id_cliente_fixgi')->on('clientes_fixgis');
            $table->foreign('id_establecimiento_fx')->references('id_establecimiento')->on('establecimientos');
            $table->foreign('id_aplicacion_web')->references('id_aplicacion_web')->on('aplicaciones_web');
            $table->foreign('id_estado')->references('id_estado')->on('estados');
        });

        DB::table('facturacion_membresias')->insert([
            [
                'id_cliente_fx' => '1',
                'id_establecimiento_fx' => '1',
                'id_aplicacion_web' => '1',
                'monto_total' => '9200000',
                'id_estado' => '8',
                'dias_restantes' => '999999',
                'fecha_pago'       => now(),
                'fecha_activacion' => now(),
            ],
            [
                'id_cliente_fx' => '2',
                'id_establecimiento_fx' => '1',
                'id_aplicacion_web' => '1',
                'monto_total' => '9200000',
                'id_estado' => '8',
                'dias_restantes' => '999999',
                'fecha_pago'       => now(),
                'fecha_activacion' => now(),
            ],

        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_membresia');
    }
};
