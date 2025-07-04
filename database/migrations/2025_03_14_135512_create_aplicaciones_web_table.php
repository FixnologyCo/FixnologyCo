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
            $table->id(); // ID autoincremental (equivalente a INT PRIMARY KEY AUTO_INCREMENT)
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->unsignedBigInteger('estilo_id')->default(1);
            $table->unsignedBigInteger('membresia_id')->nullable(false);
            $table->string('nombre_app')->nullable(false);
            $table->string('categoria_app')->nullable(false);
            $table->string('descripcion_app')->nullable(false);
           
            $table->timestamps(); 
            $table->softDeletes();

            // Si necesitas una relación con otra tabla, por ejemplo, estados:
            $table->foreign('estilo_id')->references('id')->on('estilos_app')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('membresia_id')->references('id')->on('membresias')->onDelete('cascade');
        });

        // Insertar datos iniciales
        DB::table('aplicaciones_web')->insert([

            [
                'estado_id' => '1',
                'membresia_id' => '6',
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
