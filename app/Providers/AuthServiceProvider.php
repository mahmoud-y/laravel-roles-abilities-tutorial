<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //

        Gate::before(function ($user, $ability) {
            if ($user->super) {
                return true;
            } else {
                return $user->roles()
                    ->join('ability_role', 'roles.id', '=', 'ability_role.role_id')
                    ->join('abilities', 'ability_role.ability_id', '=', 'abilities.id')
                    ->where('abilities.name', $ability)
                    ->exists();
            }
        });
    }
}
