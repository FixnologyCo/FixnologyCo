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
        Schema::create('perfil_usuario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('rol_id')->default(1);
            $table->unsignedBigInteger('indicativo_id')->nullable();
            $table->unsignedBigInteger('tipo_documento_id')->default(1);

            $table->string('ruta_foto')->nullable();
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('correo')->nullable();
            $table->string('genero')->nullable();
            $table->string('direccion_residencia')->nullable();
            $table->string('ciudad_residencia')->nullable();
            $table->string('barrio_residencia')->nullable();

            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('indicativo_id')->references('id')->on('indicativos')->onDelete('cascade');
            $table->foreign('tipo_documento_id')->references('id')->on('tipo_documentos')->onDelete('cascade');

            $table->softDeletes();

            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->onDelete('set null');

        });

        DB::table('perfil_usuario')->insert([
            [
                'usuario_id' => 1,
                'rol_id' => 4,
                'indicativo_id' => 1,
                'tipo_documento_id' => 1,
                'ruta_foto' => 'https://fixnology.co/img/empleados/jhoann_zamudio.jpg',
                'primer_nombre' => 'Jhoann',
                'segundo_nombre' => 'Sebastián',
                'primer_apellido' => 'Zamudio',
                'segundo_apellido' => 'Marulanda',
                'telefono' => '3165114410',
                'correo' => 'sebastianzamudio2004@gmail.com',
                'genero' => 'Masculino',
                'direccion_residencia' => 'Carrera 31 #17-224',
                'ciudad_residencia' => 'Bogotá D.C.',
                'barrio_residencia' => 'Soacha',
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
        Schema::dropIfExists('perfil_usuario');
    }
};
