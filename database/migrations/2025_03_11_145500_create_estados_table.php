<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEstadosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id('id_estado');
            $table->string('tipo_estado', 255)->nullable(false);
            $table->string('categoria_estado', 255)->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_modificacion')->useCurrent()->useCurrentOnUpdate();
        });

        // Insertar datos iniciales
        DB::table('estados')->insert([

            // --- Categoría: General / Ciclo de Vida ---
            ['id' => 1, 'tipo_estado' => 'Activo', 'categoria_estado' => 'General'],
            ['id' => 2, 'tipo_estado' => 'Inactivo', 'categoria_estado' => 'General'],
            ['id' => 3, 'tipo_estado' => 'Suspendido', 'categoria_estado' => 'General'],
            ['id' => 4, 'tipo_estado' => 'Archivado', 'categoria_estado' => 'General'],
            ['id' => 5, 'tipo_estado' => 'Eliminado', 'categoria_estado' => 'General'],

            // --- Categoría: Contenido ---
            ['id' => 6, 'tipo_estado' => 'Borrador', 'categoria_estado' => 'Contenido'],
            ['id' => 7, 'tipo_estado' => 'Publicado', 'categoria_estado' => 'Contenido'],

            // --- Categoría: Proceso / Flujo de Trabajo ---
            ['id' => 8, 'tipo_estado' => 'Pendiente', 'categoria_estado' => 'Proceso'],
            ['id' => 9, 'tipo_estado' => 'En progreso', 'categoria_estado' => 'Proceso'],
            ['id' => 10, 'tipo_estado' => 'En espera', 'categoria_estado' => 'Proceso'],
            ['id' => 11, 'tipo_estado' => 'En revisión', 'categoria_estado' => 'Proceso'],
            ['id' => 12, 'tipo_estado' => 'Aprobado', 'categoria_estado' => 'Proceso'],
            ['id' => 13, 'tipo_estado' => 'Rechazado', 'categoria_estado' => 'Proceso'],
            ['id' => 14, 'tipo_estado' => 'Cancelado', 'categoria_estado' => 'Proceso'],
            ['id' => 15, 'tipo_estado' => 'Finalizado', 'categoria_estado' => 'Proceso'],

            // --- Categoría: Pagos ---
            ['tipo_estado' => 'Pendiente', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Procesando', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Pagado', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Rechazado', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Cancelado', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Reembolsado', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'Vencido', 'categoria_estado' => 'Pagos'],
            ['tipo_estado' => 'En Disputa', 'categoria_estado' => 'Pagos'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
}

