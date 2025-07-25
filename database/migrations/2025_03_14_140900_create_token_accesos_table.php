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
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->unsignedBigInteger('establecimiento_id')->nullable();
            $table->string('token_activacion')->unique();
        
            $table->timestamps(); 

            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
        });

        DB::table('token_accesos')->insert([
            [
                'establecimiento_id' => '1',
                'token_activacion' => '3e1dce5b-cdc0-4487-9cb9-ecef60ae1a8b',
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
        Schema::dropIfExists('token_accesos');
    }
};
