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
    Schema::create('clientes_fixgis', function (Blueprint $table) {
        $table->id(); 
        $table->unsignedBigInteger('id_estado')->default(1);
        $table->unsignedBigInteger('id_rol')->default(1);
        $table->unsignedBigInteger('id_tienda')->nullable();

        $table->string('nombres_ct');
        $table->string('apellidos_ct');
        $table->unsignedBigInteger('id_tipo_documento');
        $table->string('numero_documento_ct')->unique();
        $table->string('contrasenia_ct');
        $table->string('email_ct')->unique();
        $table->string('telefono_ct')->unique();

        $table->longText('foto_binaria')->nullable(); // Imagen en binario

        $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

        $table->foreign('id_estado')->references('id')->on('estados')->onDelete('cascade');
        $table->foreign('id_rol')->references('id')->on('roles_administrativos')->onDelete('cascade');
        $table->foreign('id_tienda')->references('id')->on('tiendas_sistematizadas')->onDelete('cascade');
        $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos')->onDelete('cascade');
    });

    DB::table('clientes_fixgis')->insert([
        [
            'id_rol' => '4',
            'id_tienda' => '1',
            'nombres_ct'=> 'Jhoann Sebastián',
            'apellidos_ct' => 'Zamudio',
            'id_tipo_documento' => '1',
            'numero_documento_ct' => '1013580753',
            'contrasenia_ct' => '123456',
            'email_ct' => 'sebastianzamudio2004@gmail.com',
            'telefono_ct' => '3165114410',
        ],
        [
            'id_rol' => '4',
            'id_tienda' => '1',
            'nombres_ct'=> 'Erik Manuel',
            'apellidos_ct' => 'Guevara',
            'id_tipo_documento' => '1',
            'numero_documento_ct' => '777',
            'contrasenia_ct' => '123456',
            'email_ct' => 'emgladino@gmail.com',
            'telefono_ct' => '3212820011',
        ],
    ]);
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_fixgis');
    }
};
