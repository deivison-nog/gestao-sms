<?php

namespace Database\Seeders;

use App\Models\MenuPermission;
use App\Models\Professional;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect(config('menu'));

        $permissions->each(function (array $permission): void {
            MenuPermission::query()->updateOrCreate(
                ['key' => $permission['key']],
                ['label' => $permission['label']]
            );
        });

        $adminRole = Role::query()->updateOrCreate(['slug' => 'administrador'], ['name' => 'Administrador']);
        $gestorRole = Role::query()->updateOrCreate(['slug' => 'gestor'], ['name' => 'Gestor de Unidade']);
        $tecnicoRole = Role::query()->updateOrCreate(['slug' => 'tecnico'], ['name' => 'Técnico']);

        $adminRole->syncMenuPermissions($permissions->pluck('key')->all());
        $gestorRole->syncMenuPermissions(['dashboard', 'frequencia', 'profissionais', 'cronograma', 'suporte', 'enfermagem']);
        $tecnicoRole->syncMenuPermissions(['dashboard', 'cronograma', 'suporte']);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gestaosms.local'],
            [
                'name' => 'Administrador do Sistema',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $nurseUser = User::query()->updateOrCreate(
            ['email' => 'enfermeiro@gestaosms.local'],
            [
                'name' => 'Enfermeiro Exemplo',
                'password' => Hash::make('password'),
                'role_id' => $tecnicoRole->id,
            ]
        );

        Professional::query()->updateOrCreate(
            ['name' => 'Enfermeiro Exemplo'],
            [
                'position' => 'Enfermeiro',
                'unit' => 'UBS Central',
                'is_active' => true,
                'is_frequency_enabled' => true,
                'user_id' => $nurseUser->id,
            ]
        );

        Professional::query()->updateOrCreate(
            ['name' => 'Técnico de Enfermagem Exemplo'],
            [
                'position' => 'Técnico de Enfermagem',
                'unit' => 'UBS Norte',
                'is_active' => true,
                'is_frequency_enabled' => true,
            ]
        );

        Professional::query()->updateOrCreate(
            ['name' => 'Médico Exemplo'],
            [
                'position' => 'Médico',
                'unit' => 'UBS Sul',
                'is_active' => true,
                'is_frequency_enabled' => true,
            ]
        );

        if (! $admin->professional) {
            Professional::query()->create([
                'name' => 'Administrador do Sistema',
                'position' => 'Administrador',
                'unit' => 'Secretaria Municipal',
                'is_active' => true,
                'is_frequency_enabled' => false,
                'user_id' => $admin->id,
            ]);
        }
    }
}
