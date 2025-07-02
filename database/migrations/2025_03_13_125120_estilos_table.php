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
        Schema::create('estilos_app', function (Blueprint $table) {
            $table->id(); 
    
            $table->string('color_fondo'); 
            $table->string('color_texto'); 
            $table->string('color_hover_texto'); 
            $table->string('color_border'); 
            $table->string('color_shadow')->default('shadow-xl');  
            $table->string('icono')->default('function');  
        
            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
    
        });
        

        DB::table('estilos_app')->insert([
            [
                'id' => '1',
                'color_fondo' => 'bg-universal-naranja',
                'color_texto' => 'text-universal-naranja',
                'color_hover_texto' => 'hover:text-universal-naranja',
                'color_border' => 'border-b-2 border-universal-naranja',
                'color_shadow' => 'shadow-xl',
                'icono' => 'function',
            ]

        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estilos_app');
    }
};
