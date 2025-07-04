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
        Schema::create('indicativos', function (Blueprint $table) {
            $table->id(); 
            $table->string('pais');
            $table->string('ciudad_principal');
            $table->string('codigo_pais');
            $table->string('zona_horaria');
            $table->timestamps(); 

        });
        

        DB::table('indicativos')->insert([
           // América
            ['pais' => 'Colombia', 'ciudad_principal' => 'Bogotá', 'codigo_pais' => '+57', 'zona_horaria' => 'America/Bogota'],
            ['pais' => 'Estados Unidos', 'ciudad_principal' => 'Nueva York', 'codigo_pais' => '+1', 'zona_horaria' => 'America/New_York'],
            ['pais' => 'México', 'ciudad_principal' => 'Ciudad de México', 'codigo_pais' => '+52', 'zona_horaria' => 'America/Mexico_City'],
            ['pais' => 'Argentina', 'ciudad_principal' => 'Buenos Aires', 'codigo_pais' => '+54', 'zona_horaria' => 'America/Argentina/Buenos_Aires'],
            ['pais' => 'Brasil', 'ciudad_principal' => 'São Paulo', 'codigo_pais' => '+55', 'zona_horaria' => 'America/Sao_Paulo'],
            ['pais' => 'Canadá', 'ciudad_principal' => 'Toronto', 'codigo_pais' => '+1', 'zona_horaria' => 'America/Toronto'],
            ['pais' => 'Chile', 'ciudad_principal' => 'Santiago', 'codigo_pais' => '+56', 'zona_horaria' => 'America/Santiago'],
            ['pais' => 'Perú', 'ciudad_principal' => 'Lima', 'codigo_pais' => '+51', 'zona_horaria' => 'America/Lima'],
            ['pais' => 'Venezuela', 'ciudad_principal' => 'Caracas', 'codigo_pais' => '+58', 'zona_horaria' => 'America/Caracas'],
            ['pais' => 'Ecuador', 'ciudad_principal' => 'Quito', 'codigo_pais' => '+593', 'zona_horaria' => 'America/Guayaquil'],

            // Europa
            ['pais' => 'España', 'ciudad_principal' => 'Madrid', 'codigo_pais' => '+34', 'zona_horaria' => 'Europe/Madrid'],
            ['pais' => 'Reino Unido', 'ciudad_principal' => 'Londres', 'codigo_pais' => '+44', 'zona_horaria' => 'Europe/London'],
            ['pais' => 'Francia', 'ciudad_principal' => 'París', 'codigo_pais' => '+33', 'zona_horaria' => 'Europe/Paris'],
            ['pais' => 'Alemania', 'ciudad_principal' => 'Berlín', 'codigo_pais' => '+49', 'zona_horaria' => 'Europe/Berlin'],
            ['pais' => 'Italia', 'ciudad_principal' => 'Roma', 'codigo_pais' => '+39', 'zona_horaria' => 'Europe/Rome'],
            ['pais' => 'Portugal', 'ciudad_principal' => 'Lisboa', 'codigo_pais' => '+351', 'zona_horaria' => 'Europe/Lisbon'],
            ['pais' => 'Países Bajos', 'ciudad_principal' => 'Ámsterdam', 'codigo_pais' => '+31', 'zona_horaria' => 'Europe/Amsterdam'],
            ['pais' => 'Suiza', 'ciudad_principal' => 'Zúrich', 'codigo_pais' => '+41', 'zona_horaria' => 'Europe/Zurich'],
            ['pais' => 'Rusia', 'ciudad_principal' => 'Moscú', 'codigo_pais' => '+7', 'zona_horaria' => 'Europe/Moscow'],

            // Asia
            ['pais' => 'China', 'ciudad_principal' => 'Pekín', 'codigo_pais' => '+86', 'zona_horaria' => 'Asia/Shanghai'],
            ['pais' => 'Japón', 'ciudad_principal' => 'Tokio', 'codigo_pais' => '+81', 'zona_horaria' => 'Asia/Tokyo'],
            ['pais' => 'India', 'ciudad_principal' => 'Nueva Delhi', 'codigo_pais' => '+91', 'zona_horaria' => 'Asia/Kolkata'],
            ['pais' => 'Corea del Sur', 'ciudad_principal' => 'Seúl', 'codigo_pais' => '+82', 'zona_horaria' => 'Asia/Seoul'],
            ['pais' => 'Emiratos Árabes Unidos', 'ciudad_principal' => 'Dubái', 'codigo_pais' => '+971', 'zona_horaria' => 'Asia/Dubai'],
            ['pais' => 'Singapur', 'ciudad_principal' => 'Singapur', 'codigo_pais' => '+65', 'zona_horaria' => 'Asia/Singapore'],

            // África
            ['pais' => 'Sudáfrica', 'ciudad_principal' => 'Johannesburgo', 'codigo_pais' => '+27', 'zona_horaria' => 'Africa/Johannesburg'],
            ['pais' => 'Egipto', 'ciudad_principal' => 'El Cairo', 'codigo_pais' => '+20', 'zona_horaria' => 'Africa/Cairo'],
            ['pais' => 'Nigeria', 'ciudad_principal' => 'Lagos', 'codigo_pais' => '+234', 'zona_horaria' => 'Africa/Lagos'],
            ['pais' => 'Marruecos', 'ciudad_principal' => 'Casablanca', 'codigo_pais' => '+212', 'zona_horaria' => 'Africa/Casablanca'],

            // Oceanía
            ['pais' => 'Australia', 'ciudad_principal' => 'Sídney', 'codigo_pais' => '+61', 'zona_horaria' => 'Australia/Sydney'],
            ['pais' => 'Nueva Zelanda', 'ciudad_principal' => 'Auckland', 'codigo_pais' => '+64', 'zona_horaria' => 'Pacific/Auckland'],

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
