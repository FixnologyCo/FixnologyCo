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
                $table->unsignedBigInteger('id_estado')->default(1);
                $table->enum('forma_pago', [
                    "Efectivo", 
                    "Nequi", 
                    "Daviplata", 
                    "Tarjeta", 
                    "Otro medio de pago"
                    ])->nullable(false);
                $table->timestamp('fecha_creacion')->default(DB::raw(value: 'CURRENT_TIMESTAMP'));
                $table->timestamp('fecha_modificacion')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
                
                // Si necesitas una relación con otra tabla, por ejemplo, estados:
                $table->foreign('id_estado')->references('id')->on('estados')->onDelete('cascade');
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
