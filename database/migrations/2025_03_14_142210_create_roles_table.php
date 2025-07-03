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
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->enum('tipo_rol', ["Administrador", "Empleado", "Cliente", "SuperAdmin"])->nullable(false);
            $table->boolean('editar')->nullable(false);
            $table->boolean('eliminar')->nullable(false);
            $table->boolean('crear')->nullable(false);
            $table->boolean('ver')->nullable(false);


            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
            
            // Si necesitas una relación con otra tabla, por ejemplo, estados:
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');

        });

        DB::table('roles')->insert([
            ['tipo_rol' => 'Administrador' , 'editar' => true, 'eliminar' => true, 'crear' => true, 'ver' => true],
            ['tipo_rol' => 'Empleado', 'editar' => true, 'eliminar' => false, 'crear' => true, 'ver' => true],
            ['tipo_rol' => 'Cliente', 'editar' => false, 'eliminar' => false, 'crear' => false, 'ver' => true],
            ['tipo_rol' => 'SuperAdmin', 'editar' => true, 'eliminar' => true, 'crear' => true, 'ver' => true],
            
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
