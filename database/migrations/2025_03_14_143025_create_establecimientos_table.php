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
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id(); // ID autoincremental (equivalente a INT PRIMARY KEY AUTO_INCREMENT)
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->unsignedBigInteger('token_id')->nullable();
            $table->unsignedBigInteger('aplicacion_web_id')->nullable();
            $table->unsignedBigInteger('propietario_id')->nullable();

            $table->string('ruta_foto_establecimiento')->nullable();
            $table->string('tipo_establecimiento')->nullable();
            $table->string('nombre_establecimiento')->nullable();
            $table->string('email_establecimiento')->nullable();
            $table->string('telefono_establecimiento')->nullable();
            $table->string('direccion_establecimiento')->nullable();
            $table->string('barrio_establecimiento')->nullable();
            $table->string('ciudad_establecimiento')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->onDelete('set null');

            $table->foreign('token_id')->references('id')->on('token_accesos')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('aplicacion_web_id')->references('id')->on('aplicaciones_web')->onDelete('cascade');
            $table->foreign('propietario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
        
        DB::table('establecimientos')->insert([
            [
                'token_id' => 1,
                'aplicacion_web_id' => 1,
                'propietario_id' => 1,
                'tipo_establecimiento' => 'Desarrollo de TI',
                'nombre_establecimiento' => 'Fixnology CO',
                'email_establecimiento' => 'fixnologyco@gmail.com',
                'telefono_establecimiento' => '3219631459',
                'direccion_establecimiento' => 'Conjunto Naranjo',
                'barrio_establecimiento' => 'NA',
                'ciudad_establecimiento' => 'Bogotá',
                'ruta_foto_establecimiento' => 'https://fixnology.co/img/logo.png',
                'created_at' => now(),
                'updated_at' => now(),
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
        Schema::dropIfExists('tiendas_sistematizadas');
        Schema::dropIfExists('usuarios');
    }
};
