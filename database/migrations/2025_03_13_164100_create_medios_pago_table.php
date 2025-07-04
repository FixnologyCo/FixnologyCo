<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateMediosPagoTable extends Migration
{
    public function up(): void
    {
        
            Schema::create('medios_pago', function (Blueprint $table) {
                $table->id(); 
                $table->unsignedBigInteger('estado_id')->default(1);
                $table->string('forma_pago')->nullable(false);
                $table->timestamps();

                
                $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            });

            // Insertar datos iniciales
            DB::table('medios_pago')->insert([
            ['forma_pago' => 'Efectivo'],
            ['forma_pago' => 'Nequi'],
            ['forma_pago' => 'Daviplata'],
            ['forma_pago' => 'Tarjeta'],
            ['forma_pago' => 'Otro medio de pago'],
        ]);
    }

    
    public function down(): void
    {
        Schema::dropIfExists('medios_pago');
    }
    }
