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
        Schema::create('estilos_app', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_relacion');
            $table->string('primary');
            $table->string('secondary');
            $table->string('icono')->default('function');
            $table->timestamps();
             $table->softDeletes();
        });

        // Seeder
        DB::table('estilos_app')->insert([
            'id' => 1,
            'nombre_relacion' => 'Fixnology App',
            'primary' => '#f05235',
            'secondary' => '#83B4FF',
            'icono' => 'function',
            'created_at' => now(),
            'updated_at' => now()
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
