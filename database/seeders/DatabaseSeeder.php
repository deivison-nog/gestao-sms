<?php

namespace Database\Seeders;

use App\Models\MenuPermission;
use App\Models\Position;
use App\Models\Professional;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -- Menu permissions --
        $permissions = collect(config('menu'));
        $permissions->each(function (array $permission): void {
            MenuPermission::query()->updateOrCreate(
                ['key' => $permission['key']],
                ['label' => $permission['label']]
            );
        });

        // -- Roles --
        $adminRole  = Role::query()->updateOrCreate(['slug' => 'administrador'], ['name' => 'Administrador']);
        $gestorRole = Role::query()->updateOrCreate(['slug' => 'gestor'],        ['name' => 'Gestor de Unidade']);
        $tecnicoRole = Role::query()->updateOrCreate(['slug' => 'tecnico'],      ['name' => 'Técnico']);

        $adminRole->syncMenuPermissions($permissions->pluck('key')->all());
        $gestorRole->syncMenuPermissions(['dashboard', 'frequencia', 'profissionais', 'cronograma', 'suporte', 'enfermagem']);
        $tecnicoRole->syncMenuPermissions(['dashboard', 'cronograma', 'suporte']);

        // -- Positions (funções) --
        $positionNames = [
            'Administrador',
            'Enfermeiro',
            'Técnico de Enfermagem',
            'Médico',
            'Agente Comunitário de Saúde',
            'Dentista',
            'Fisioterapeuta',
            'Assistente Social',
            'Gestor de Unidade',
        ];

        $positions = [];
        foreach ($positionNames as $pName) {
            $positions[$pName] = Position::query()->updateOrCreate(
                ['name' => $pName],
                ['is_active' => true]
            );
        }

        // -- Users --
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gestaosms.local'],
            [
                'name'     => 'Administrador do Sistema',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
            ]
        );

        $nurseUser = User::query()->updateOrCreate(
            ['email' => 'enfermeiro@gestaosms.local'],
            [
                'name'     => 'Enfermeiro Exemplo',
                'password' => Hash::make('password'),
                'role_id'  => $tecnicoRole->id,
            ]
        );

        // -- Professionals --
        Professional::query()->updateOrCreate(
            ['cpf' => '111.111.111-11'],
            [
                'name'           => 'Enfermeiro Exemplo',
                'birth_date'     => '1985-06-15',
                'class_registry' => 'COREN-PE 123456',
                'position_id'    => $positions['Enfermeiro']->id,
                'workload'       => '40h',
                'contract_type'  => 'efetivo',
                'unit'           => 'UBS Central',
                'is_active'      => true,
                'is_frequency_enabled' => true,
                'user_id'        => $nurseUser->id,
            ]
        );

        Professional::query()->updateOrCreate(
            ['cpf' => '222.222.222-22'],
            [
                'name'           => 'Técnico de Enfermagem Exemplo',
                'birth_date'     => '1990-03-20',
                'class_registry' => 'COREN-PE 654321',
                'position_id'    => $positions['Técnico de Enfermagem']->id,
                'workload'       => '40h',
                'contract_type'  => 'efetivo',
                'unit'           => 'UBS Norte',
                'is_active'      => true,
                'is_frequency_enabled' => true,
            ]
        );

        Professional::query()->updateOrCreate(
            ['cpf' => '333.333.333-33'],
            [
                'name'          => 'Médico Exemplo',
                'birth_date'    => '1978-11-05',
                'class_registry' => 'CRM-PE 54321',
                'position_id'   => $positions['Médico']->id,
                'workload'      => '20h',
                'contract_type' => 'temporario',
                'unit'          => 'UBS Sul',
                'is_active'     => true,
                'is_frequency_enabled' => true,
            ]
        );

        if (! $admin->professional) {
            Professional::query()->create([
                'name'          => 'Administrador do Sistema',
                'cpf'           => '000.000.000-00',
                'position_id'   => $positions['Administrador']->id,
                'unit'          => 'Secretaria Municipal',
                'is_active'     => true,
                'is_frequency_enabled' => false,
                'user_id'       => $admin->id,
            ]);
        }
    }
}
