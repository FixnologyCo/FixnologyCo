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
        Schema::create('token_accesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_estado')->default(1);
            $table->unsignedBigInteger('id_establecimiento')->nullable();
            $table->string('token_activacion')->unique();
        
            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();

            $table->foreign('id_estado')->references('id')->on('estados')->onDelete('cascade');
        });

        DB::table('token_accesos')->insert([
            [
                'id_establecimiento' => '1',
                'token_activacion' => '3e1dce5b-cdc0-4487-9cb9-ecef60ae1a8b',
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_accesos');
    }
};
