<?php

namespace App\Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\AccessControl\Models\Role;

class AclDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or update the Super Admin role
        $adminRole = Role::updateOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Acesso completo e irrestrito']
        );

        // 2. Create or update the user Alessandro
        $user = User::updateOrCreate(
            ['email' => 'alessandro.souza@norte.dev.br'],
            [
                'name' => 'Alessandro Souza',
                'password' => Hash::make('skyorhell')
            ]
        );

        // 3. Assign the Super Admin role to the user efficiently
        if (!$user->roles()->where('roles.id', $adminRole->id)->exists()) {
            $user->roles()->attach($adminRole->id);
        }

        // 4. Create the Pos/Cashier Role and User
        $caixaRole = Role::updateOrCreate(
            ['name' => 'Caixa Operacional'],
            ['description' => 'Acesso apenas a Frente de Loja (PDV)']
        );

        $caixaUser = User::updateOrCreate(
            ['email' => 'caixa@norte.dev.br'],
            [
                'name' => 'Paulo (Caixa)',
                'password' => Hash::make('skyorhell')
            ]
        );

        if (!$caixaUser->roles()->where('roles.id', $caixaRole->id)->exists()) {
            $caixaUser->roles()->attach($caixaRole->id);
        }
    }
}
