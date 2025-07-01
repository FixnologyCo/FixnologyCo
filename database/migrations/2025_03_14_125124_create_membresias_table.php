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
        Schema::create('membresias', function (Blueprint $table) {
            $table->id('id_membresia'); 
            $table->unsignedBigInteger('id_estilo');
            $table->unsignedBigInteger('id_estado')->default(1); 
        
            $table->string('nombre_membresia'); 
            $table->string('precio_membresia'); 
            $table->string('periodo_duracion'); 
            $table->integer('duracion_membresia'); 
            $table->text('descripcion_corta')->nullable(); 
        
            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
    
            $table->foreign('id_estado')->references('id_estado')->on('estados')->onDelete('cascade');
            $table->foreign('id_estilo')->references('id_estilo')->on('estilos_app')->onDelete('cascade');
        });
        

        DB::table('membresias')->insert([
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'Free',
                'precio_membresia' => '0',
                'periodo_duracion'=> 'Semanal',
                'duracion_membresia' => '7',
                'descripcion_corta' => 'Plan de prueba totalmente gratuito.',
            ],
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'Platino',
                'precio_membresia' => '75800',
                'periodo_duracion'=> 'Mensual',
                'duracion_membresia' => '30',
                'descripcion_corta' => 'Plan de modalidad mensual, con previo aviso 5 dias antes.',
            ],
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'Diamante',
                'precio_membresia' => '210400',
                'periodo_duracion'=> 'Trimestral',
                'duracion_membresia' => '90',
                'descripcion_corta' => 'Plan de modalidad cada 3 meses, con previo aviso 5 dias antes',
            ],
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'Esmeralda',
                'precio_membresia' => '435000',
                'periodo_duracion'=> 'Semestral',
                'duracion_membresia' => '180',
                'descripcion_corta' => 'Plan de modalidad cada 6 meses, con previo aviso 5 dias antes',
            ],
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'Ruby',
                'precio_membresia' => '1285000',
                'periodo_duracion'=> 'Anual',
                'duracion_membresia' => '365',
                'descripcion_corta' => 'Plan de modalidad cada 12 meses, con previo aviso 5 dias antes',
            ],
            [
                'estilo_id' => '1',
                'nombre_membresia' => 'All star',
                'precio_membresia' => '9200000',
                'periodo_duracion'=> 'Infinito',
                'duracion_membresia' => '999999',
                'descripcion_corta' => 'Plan sin terminación.',
            ],

        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
