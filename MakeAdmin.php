<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--E|email=admin@admin.com} {--P|password=$admin.2022} {--N|name=Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un usuario administrador del sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $role = Role::where('name', 'super-admin')->first();
        $this->info('Verificando rol...');
        if ($role) {
            $this->info('Creando usuario...');
            $user = User::create([
                'name' => $this->option('name'),
                'email' => $this->option('email'),
                'password' => Hash::make($this->option('password')),
            ]);
            $this->info('Asignando rol...');
            $user->assignRole('super-admin');
            $this->info('Super administrador creado eon éxito !!');
        } else {
            $this->error('No se ha encontrado el role de super administrador, recuerde ejecutar las migraciones y los seeders');
        }
    }
}
