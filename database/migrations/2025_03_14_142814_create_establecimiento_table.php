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
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id('id_establecimiento'); // ID autoincremental (equivalente a INT PRIMARY KEY AUTO_INCREMENT)
            $table->unsignedBigInteger('id_estado')->default(1);
            $table->unsignedBigInteger('id_token')->nullable();
            $table->unsignedBigInteger('id_aplicacion_web')->nullable();
            $table->unsignedBigInteger('id_propietario')->nullable();
            $table->string('nombre_establecimiento');
            $table->string('email_establecimiento')->unique();
            $table->string('telefono_establecimiento')->unique();
            $table->string('direccion_establecimiento');
            $table->string('barrio_establecimiento');
            $table->string('ciudad_establecimiento');
            $table->longText('foto_establecimiento');

            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_estado')->references('id_estado')->on('estados')->onDelete('cascade');
            $table->foreign('id_aplicacion_web')->references('id_aplicacion_web')->on('aplicaciones_web')->onDelete('cascade');
            $table->foreign('id_propietario')->references('id_cliente_fixgi')->on('clientes_fixnology')->onDelete('cascade');
        });

        DB::table('establecimientos')->insert([
            [
                'id_token' => '1',
                'id_aplicacion_web' => '1',
                'nombre_establecimiento'=> 'Fixnology CO',
                'email_establecimiento' => 'fixnologyco@gmail.com',
                'telefono_establecimiento' => '3219631459',
                'direccion_establecimiento' => 'Conjunto Naranjo',
                'barrio_establecimiento' => 'NA',
                'ciudad_establecimiento' => 'Bogotá',
                'foto_establecimiento' => 'https://fixnology.co/img/logo.png',
            ],
        ]);     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas_sistematizadas');
    }
};
