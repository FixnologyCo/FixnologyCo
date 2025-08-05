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
             $table->softDeletes();


            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
        });

        // Insertar datos iniciales
        DB::table('medios_pago')->insert([
            [
                'forma_pago' => 'Efectivo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Transferencia bancaria',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Pago contra entrega',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Pago con tarjeta de crédito',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Pago con tarjeta de débito',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Pago en línea',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'PSE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Billetera virtual',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Nequi',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'forma_pago' => 'Daviplata',
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);
    }


    public function down(): void
    {
        Schema::dropIfExists('medios_pago');
    }
}
