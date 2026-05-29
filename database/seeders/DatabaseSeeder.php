<?php

namespace Database\Seeders;

use App\Models\MenuPermission;
use App\Models\Establishment;
use App\Models\Position;
use App\Models\Professional;
use App\Models\Role;
use Illuminate\Database\Seeder;

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

        // -- Establishments --
        $establishmentNames = [
            ['name' => 'UBS Central', 'cnes' => '0000001', 'address' => 'Centro'],
            ['name' => 'UBS Sul', 'cnes' => '0000002', 'address' => 'Bairro Sul'],
            ['name' => 'ESF Norte', 'cnes' => '0000003', 'address' => 'Bairro Norte'],
        ];

        foreach ($establishmentNames as $establishment) {
            Establishment::query()->updateOrCreate(
                ['name' => $establishment['name']],
                $establishment
            );
        }

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

        $this->call(TestProfilesSeeder::class);

        // -- Professionals --

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

    }
}
