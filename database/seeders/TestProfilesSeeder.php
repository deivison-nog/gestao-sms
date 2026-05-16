<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Professional;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestProfilesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'administrador' => Role::query()->firstWhere('slug', 'administrador'),
            'gestor' => Role::query()->firstWhere('slug', 'gestor'),
            'tecnico' => Role::query()->firstWhere('slug', 'tecnico'),
        ];

        $positions = [
            'Administrador' => Position::query()->firstWhere('name', 'Administrador'),
            'Gestor de Unidade' => Position::query()->firstWhere('name', 'Gestor de Unidade'),
            'Enfermeiro' => Position::query()->firstWhere('name', 'Enfermeiro'),
            'Técnico de Enfermagem' => Position::query()->firstWhere('name', 'Técnico de Enfermagem'),
        ];

        $profiles = [
            [
                'name' => 'Administrador do Sistema',
                'email' => 'admin@gestaosms.local',
                'password' => 'password',
                'role_slug' => 'administrador',
                'cpf' => '000.000.000-00',
                'birth_date' => '1980-01-01',
                'class_registry' => null,
                'position_name' => 'Administrador',
                'workload' => '40h',
                'contract_type' => 'efetivo',
                'unit' => 'Secretaria Municipal',
                'is_frequency_enabled' => false,
            ],
            [
                'name' => 'Gestor de Unidade Exemplo',
                'email' => 'gestor@gestaosms.local',
                'password' => 'password',
                'role_slug' => 'gestor',
                'cpf' => '444.444.444-44',
                'birth_date' => '1982-09-10',
                'class_registry' => null,
                'position_name' => 'Gestor de Unidade',
                'workload' => '40h',
                'contract_type' => 'efetivo',
                'unit' => 'UBS Central',
                'is_frequency_enabled' => true,
            ],
            [
                'name' => 'Enfermeiro Exemplo',
                'email' => 'enfermeiro@gestaosms.local',
                'password' => 'password',
                'role_slug' => 'tecnico',
                'cpf' => '111.111.111-11',
                'birth_date' => '1985-06-15',
                'class_registry' => 'COREN-PE 123456',
                'position_name' => 'Enfermeiro',
                'workload' => '40h',
                'contract_type' => 'efetivo',
                'unit' => 'UBS Central',
                'is_frequency_enabled' => true,
            ],
            [
                'name' => 'Técnico de Enfermagem Exemplo',
                'email' => 'tecnico@gestaosms.local',
                'password' => 'password',
                'role_slug' => 'tecnico',
                'cpf' => '222.222.222-22',
                'birth_date' => '1990-03-20',
                'class_registry' => 'COREN-PE 654321',
                'position_name' => 'Técnico de Enfermagem',
                'workload' => '40h',
                'contract_type' => 'efetivo',
                'unit' => 'UBS Norte',
                'is_frequency_enabled' => true,
            ],
        ];

        foreach ($profiles as $profile) {
            if (! isset($roles[$profile['role_slug']]) || ! $roles[$profile['role_slug']] || ! isset($positions[$profile['position_name']]) || ! $positions[$profile['position_name']]) {
                continue;
            }

            $user = User::query()->updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Hash::make($profile['password']),
                    'role_id' => $roles[$profile['role_slug']]->id,
                ]
            );

            Professional::query()->updateOrCreate(
                ['cpf' => $profile['cpf']],
                [
                    'name' => $profile['name'],
                    'birth_date' => $profile['birth_date'],
                    'class_registry' => $profile['class_registry'],
                    'position_id' => $positions[$profile['position_name']]->id,
                    'workload' => $profile['workload'],
                    'contract_type' => $profile['contract_type'],
                    'unit' => $profile['unit'],
                    'is_active' => true,
                    'is_frequency_enabled' => $profile['is_frequency_enabled'],
                    'user_id' => $user->id,
                ]
            );
        }
    }
}
