<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Ability;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
