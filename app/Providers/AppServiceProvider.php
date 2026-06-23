<?php

namespace App\Providers;

use App\Models\MenuPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $menu = collect(config('menu'));
            $user = Auth::user();

            if (! $user) {
                $view->with('sidebarMenu', collect());

                return;
            }

            $view->with('sidebarMenu', $menu->filter(fn (array $item) => $user->hasMenuAccess($item['key']))->values());
            $view->with('allMenuPermissions', MenuPermission::query()->pluck('label', 'key'));
        });
    }
}
