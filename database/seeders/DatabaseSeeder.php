<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Eliminar y crear el directorio vouchers en storage/app/public
        Storage::disk('public')->deleteDirectory('vouchers');
        Storage::disk('public')->makeDirectory('vouchers');


        Permission::Create(['name' => 'trade']);

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $traderRole = Role::firstOrCreate(['name' => 'trader']);

        // Asignar permisos
        $adminRole->givePermissionTo('trade'); // Admin puede operar
        $traderRole->givePermissionTo('trade'); // Trader puede operar

        // Crear usuario de prueba (trader)
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'wallet_address' => 'TTestUserWallet123',
        ]);
        $testUser->assignRole('trader');

        // Crear usuario administrador
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'wallet_address' => 'TAdminWallet456',
        ]);
        $adminUser->assignRole('admin');
    }
}
