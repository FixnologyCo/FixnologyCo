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
            $table->id(); // ID autoincremental
            $table->unsignedBigInteger('id_cliente_fx');
            $table->unsignedBigInteger('id_establecimiento_fx');
            $table->unsignedBigInteger('id_aplicacion_web');
            $table->unsignedBigInteger('id_estado');
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->string('medio_pago')->default('NA');
            $table->integer('dias_restantes')->nullable();
        
            $table->timestamp('fecha_pago')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_cliente_fx')->references('id')->on('clientes_fixnology');
            $table->foreign('id_establecimiento_fx')->references('id')->on('establecimientos');
            $table->foreign('id_aplicacion_web')->references('id')->on('aplicaciones_web');
            $table->foreign('id_estado')->references('id')->on('estados');
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
               
            ],
            [
                'id_cliente_fx' => '2',
                'id_establecimiento_fx' => '1',
                'id_aplicacion_web' => '1',
                'monto_total' => '9200000',
                'id_estado' => '8',
                'dias_restantes' => '999999',
                'fecha_pago'       => now(),
                
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
