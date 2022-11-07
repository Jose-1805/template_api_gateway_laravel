<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("id")->unique()->comment('Identificador único de cada registro');
            $table->string('name')->comment('Nombre completo del usuario');
            $table->string('email')->unique()->comment('Correo electrónico del usuario');
            $table->timestamp('email_verified_at')->nullable()->comment('Fecha de verificación del correo electrónico del usuario');
            $table->string('password')->nullable()->comment('Contraseña de acceso del usuario');
            $table->rememberToken()->comment('Token de recordatorio de la sesión del usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
