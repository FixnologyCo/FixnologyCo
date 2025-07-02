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
        Schema::create('clientes_fixnology', function (Blueprint $table) {
            $table->id(); // ID autoincremental (equivalente a INT PRIMARY KEY AUTO_INCREMENT)
            $table->unsignedBigInteger('id_estado')->default(1);
            $table->unsignedBigInteger('id_rol')->default(1);
            
            $table->unsignedBigInteger('id_indicativo')->nullable();
            $table->unsignedBigInteger('id_tipo_documento')->default(1);
            $table->unsignedBigInteger('id_medio_pago')->default(1);

            $table->string('primer_nombre_fx')->nullable();
            $table->string('segundo_nombre_fx')->nullable();
            $table->string('primer_apellido_fx')->nullable();
            $table->string('segundo_apellido_fx')->nullable();

            $table->string('numero_documento_fx')->unique();
            $table->string('contrasenia_fx')->nullable();
            $table->string('email_fx')->unique();
            $table->string('telefono_fx')->unique();
            $table->longText('foto_binaria_fx')->nullable();

            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_estado')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('roles_administrativos')->onDelete('cascade');
           
            $table->foreign('id_medio_pago')->references('id')->on('medios_pago')->onDelete('cascade');
            $table->foreign('id_indicativo')->references('id')->on('indicativos')->onDelete('cascade');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos')->onDelete('cascade');
        });

        DB::table('clientes_fixnology')->insert([
            [
                'id_rol' => '4',
                
                'id_tipo_documento' => '1',
                'id_indicativo' => '1',
                'primer_nombre_fx' => 'Jhoann',
                'segundo_nombre_fx' => 'Sebastián',
                'primer_apellido_fx' => 'Zamudio',
                'segundo_apellido_fx' => 'Marulanda',
                'numero_documento_fx' => '1013580753',
                'contrasenia_fx' => '123456',
                'email_fx' => 'sebastianzamudio2004@gmail.com',
                'telefono_fx' => '3165114410',
            ],
            [
                'id_rol' => '4',
                
                'id_tipo_documento' => '1',
                'id_indicativo' => '1',
                'primer_nombre_fx' => 'Erik',
                'segundo_nombre_fx' => 'Manuel',
                'primer_apellido_fx' => 'Guevara',
                'segundo_apellido_fx' => 'Ladino',
                'numero_documento_fx' => '123',
                'contrasenia_fx' => '123456',
                'email_fx' => 'emgladino@gmail.com',
                'telefono_fx' => '0000000000',
            ],
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_fixnology');
    }
};
