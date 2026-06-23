<?php

namespace Tests\Feature;

use App\Models\MenuPermission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_see_allowed_menu_items(): void
    {
        $role = Role::query()->create(['name' => 'Gestor', 'slug' => 'gestor']);
        $dashboard = MenuPermission::query()->create(['key' => 'dashboard', 'label' => 'Dashboard']);
        $support = MenuPermission::query()->create(['key' => 'suporte', 'label' => 'Suporte']);
        $role->menuPermissions()->attach([$dashboard->id]);

        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertDontSee('Suporte');

        $role->menuPermissions()->attach([$support->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSee('Suporte');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $role = Role::query()->create(['name' => 'Sem Frequência', 'slug' => 'sem-frequencia']);
        MenuPermission::query()->create(['key' => 'frequencia', 'label' => 'Frequência']);

        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)->get('/attendance')->assertForbidden();
    }
}
