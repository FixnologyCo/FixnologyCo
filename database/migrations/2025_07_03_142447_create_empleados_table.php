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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_estado')->default(1); 
            $table->unsignedBigInteger('id_establecimiento')->nullable();
            $table->unsignedBigInteger('id_rol')->default(1);
            $table->unsignedBigInteger('id_indicativo')->nullable();
            $table->unsignedBigInteger('id_tipo_documento')->default(1);

            $table->string('foto_empleado')->nullable();
            $table->string('primer_nombre_empleado')->nullable();
            $table->string('segundo_nombre_empleado')->nullable();
            $table->string('primer_apellido_empleado')->nullable();
            $table->string('segundo_apellido_empleado')->nullable();
            $table->string('numero_documento_empleado')->nullable();
            $table->string('telefono_empleado', 25)->nullable();
            $table->string('correo_empleado')->nullable();
            $table->string('genero_empleado')->nullable();
            $table->string('direccion_residencia_empleado')->nullable();
            $table->string('ciudad_residencia_empleado')->nullable();
            $table->string('barrio_residencia_empleado')->nullable();
            $table->string('cargo_empleado')->nullable();
            $table->decimal('salario_base_empleado', 10, 2)->nullable();
            $table->string('medio_pago_empleado')->nullable();
            $table->string('cuenta_ahorros_empleado')->nullable();
            $table->string('banco_empleado')->nullable();
            $table->string('horario_empleado')->nullable();
            $table->string('tipo_contrato_empleado')->nullable();
            $table->string('documentos_zip_empleado')->nullable();

            $table->timestamp('fecha_ingreso')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('fecha_egreso')->default('No aplica');
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_estado')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('id_establecimiento')->references('id')->on('establecimientos')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('roles_administrativos')->onDelete('cascade');
            $table->foreign('id_indicativo')->references('id')->on('indicativos')->onDelete('cascade');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos')->onDelete('cascade');

        });

        DB::table('empleados')->insert([
            [
                
                'id_establecimiento' => 1,
                'id_rol' => 4,
                'id_indicativo' => 1,
                'id_tipo_documento' => 1,
                'primer_nombre_empleado' => 'Jhoann',
                'segundo_nombre_empleado' => 'Sebastián',
                'primer_apellido_empleado' => 'Zamudio',
                'segundo_apellido_empleado' => 'Marulanda',
                'numero_documento_empleado' => '1013580753',
                'telefono_empleado' => '3165114410',
                'correo_empleado' => 'sebastianzamudio2004@gmail.com',
                'genero_empleado'=> 'Masculino',
                'direccion_residencia_empleado' => 'Carrera 31 #17-224',
                'ciudad_residencia_empleado' => 'Bogotá D.C.',
                'barrio_residencia_empleado' => 'Soacha',
                'cargo_empleado' => 'Gerente',
                'salario_base_empleado' => 3000000.00,
                'medio_pago_empleado' => 'Transferencia',
                'cuenta_ahorros_empleado' => '3165114410',
                'banco_empleado' => 'Nu',
                'horario_empleado' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                'tipo_contrato_empleado' => 'Indefinido',
                'documentos_zip_empleado' => 'documentos.zip',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
