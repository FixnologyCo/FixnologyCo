<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturacion_membresias', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('establecimiento_id');
            $table->unsignedBigInteger('aplicacion_web_id');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('medio_pago_id')->default(1);

            $table->decimal('monto_total', 10, 2)->default(0);
            $table->integer('dias_restantes')->nullable();

            $table->timestamp('fecha_pago')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
            $table->timestamps();
             $table->softDeletes();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->onDelete('set null');


            $table->foreign('cliente_id')->references('id')->on('usuarios');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->foreign('aplicacion_web_id')->references('id')->on('aplicaciones_web');
            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('medio_pago_id')->references('id')->on('medios_pago');
        });

        DB::table('facturacion_membresias')->insert([
            [
                'cliente_id' => '1',
                'establecimiento_id' => '1',
                'aplicacion_web_id' => '1',
                'monto_total' => '9200000',
                'estado_id' => '18',
                'dias_restantes' => '999999',
                'fecha_pago' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 1,
                'updated_by' => 1,

            ],
            [
                'cliente_id' => '2',
                'establecimiento_id' => '1',
                'aplicacion_web_id' => '1',
                'monto_total' => '9200000',
                'estado_id' => '18',
                'dias_restantes' => '999999',
                'fecha_pago' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 1,
                'updated_by' => 1,

            ]

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
