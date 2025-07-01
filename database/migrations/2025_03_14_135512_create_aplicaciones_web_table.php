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
        Schema::create('aplicaciones_web', function (Blueprint $table) {
            $table->id('id_aplicacion_web'); // ID autoincremental (equivalente a INT PRIMARY KEY AUTO_INCREMENT)
            $table->unsignedBigInteger('id_estado')->default(1);
            $table->unsignedBigInteger('id_estilo')->default(1);
            $table->unsignedBigInteger('id_membresia')->nullable(false);
            $table->string('nombre_app')->nullable(false);
            $table->string('categoria_app')->nullable(false);
            $table->string('descripcion_app')->nullable(false);
           
            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            // Si necesitas una relación con otra tabla, por ejemplo, estados:
            $table->foreign('id_estilo')->references('id_estilo')->on('estilos_app')->onDelete('cascade');
            $table->foreign('id_estado')->references('id_estado')->on('estados')->onDelete('cascade');
            $table->foreign('id_membresia')->references('id_membresia')->on('membresias')->onDelete('cascade');
        });

        // Insertar datos iniciales
        DB::table('aplicaciones_web')->insert([

            [
                'id_estado' => '1',
                'id_membresia' => '6',
                'nombre_app' => 'FixnologyCO',
                'categoria_app' => 'Aplicación Web',
                'descripcion_app' => 'Aplicación corporativa a escala Fixnology',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aplicaciones_web');
    }
};
