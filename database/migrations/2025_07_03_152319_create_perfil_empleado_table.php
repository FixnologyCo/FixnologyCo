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
        Schema::create('perfil_empleado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('establecimiento_id')->nullable();
            $table->unsignedBigInteger('rol_id')->default(1);
            $table->unsignedBigInteger('medio_pago_id')->default(1);

            $table->string('cargo')->nullable();
            $table->decimal('salario_base', 10, 2)->nullable();
            $table->string('cuenta_ahorros')->nullable();
            $table->string('banco')->nullable();
            $table->string('horario')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->string('documentos_zip')->nullable();
            $table->timestamp('fecha_ingreso')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_egreso')->nullable();
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
             $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('medio_pago_id')->references('id')->on('medios_pago')->onDelete('cascade');
        });

        DB::table('perfil_empleado')->insert([
            [
                'usuario_id' => 1,
                'establecimiento_id' => 1,
                'rol_id' => 4,
                'cargo' => 'Gerente',
                'salario_base' => 3000000.00,
                'medio_pago_id' => 1,
                'cuenta_ahorros' => '3165114410',
                'banco' => 'Nu',
                'horario' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                'tipo_contrato' => 'Indefinido',
                'documentos_zip' => 'documentos.zip',
                'fecha_ingreso' => now(),
                'fecha_egreso' => null,
                'fecha_modificacion' => now(),
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'usuario_id' => 2,
                'establecimiento_id' => 1,
                'rol_id' => 4,
                'cargo' => 'CEO',
                'salario_base' => 3000000.00,
                'medio_pago_id' => 1,
                'cuenta_ahorros' => '00000000',
                'banco' => 'Nu',
                'horario' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                'tipo_contrato' => 'Indefinido',
                'documentos_zip' => 'documentos.zip',
                'fecha_ingreso' => now(),
                'fecha_egreso' => null,
                'fecha_modificacion' => now(),
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_empleado');
    }
};
