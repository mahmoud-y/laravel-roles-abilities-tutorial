<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superuser:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Super User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('super', true)->delete();
        User::create([
            'email' => config('auth.super_user.email'),
            'name' => 'super',
            'super' => true,
            'password' => Hash::make(config('auth.super_user.password')),
        ]);
    }
}
