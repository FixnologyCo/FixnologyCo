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
        Schema::create('perfil_empleado', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('estado_id')->default(1); 
             $table->unsignedBigInteger('usuario_id'); 
            $table->unsignedBigInteger('establecimiento_id')->nullable();
            $table->unsignedBigInteger('rol_id')->default(1);

            $table->string('cargo')->nullable();
            $table->decimal('salario_base', 10, 2)->nullable();
            $table->string('medio_pago')->nullable();
            $table->string('cuenta_ahorros')->nullable();
            $table->string('banco')->nullable();
            $table->string('horario')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->string('documentos_zip')->nullable();

             $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');

        });

        DB::table('perfil_empleado')->insert([
            [
                'usuario_id' => 1,
                'establecimiento_id' => 1,
                'rol_id' => 4,
                'cargo' => 'Gerente',
                'salario_base' => 3000000.00,
                'medio_pago' => 'Transferencia',
                'cuenta_ahorros' => '3165114410',
                'banco' => 'Nu',
                'horario' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                'tipo_contrato' => 'Indefinido',
                'documentos_zip' => 'documentos.zip',
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
