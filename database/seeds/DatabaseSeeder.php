<?php

use App\Ability;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'user@example.com',
            'name' => 'user',
            'password' => Hash::make('secret'),
        ]);
        $role = $user->roles()->create(['name' => 'User']);
        $role->abilities()->attach(Ability::whereIn('name', ['view-any-user', 'view-any-role'])->pluck('id'));
    }
}
