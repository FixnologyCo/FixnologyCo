<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTipoDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('documento_legal')->nullable(false);
            $table->timestamps();

        });

        // Insertar datos iniciales
        DB::table('tipo_documentos')->insert([
            [
                'documento_legal' => 'Cedula ciudadania',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'documento_legal' => 'Tarjeta identidad',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'documento_legal' => 'Cedula extranjeria',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'documento_legal' => 'Pasaporte',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
}
