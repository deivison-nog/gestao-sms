<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('menu_permissions')->updateOrInsert(
            ['key' => 'admin_estabelecimentos'],
            ['label' => 'Administração/Estabelecimentos', 'updated_at' => $now, 'created_at' => $now]
        );

        $permissionId = DB::table('menu_permissions')->where('key', 'admin_estabelecimentos')->value('id');
        $roleIds = DB::table('roles')->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('role_menu_permission')->updateOrInsert(
                ['role_id' => $roleId, 'menu_permission_id' => $permissionId],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('menu_permissions')->where('key', 'admin_estabelecimentos')->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('role_menu_permission')->where('menu_permission_id', $permissionId)->delete();
    }
};
