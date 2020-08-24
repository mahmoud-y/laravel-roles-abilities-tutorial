<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::where('super', true)->exists()) {
            User::where('super', true)->delete();
        }
        User::create([
            'email' => config('auth.super_user.email'),
            'name' => 'super',
            'super' => true,
            'password' => Hash::make(config('auth.super_user.password'))
        ]);
    }
}
