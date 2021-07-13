<?php

namespace App\Console\Commands;

use App\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAbilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abilities:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync abilities';

    /**
     * The abilities.
     *
     * @var string
     */
    protected $abilities = [
        'view-any-user', 'view-user', 'create-user', 'update-user', 'delete-user',
        'view-any-role', 'view-role', 'create-role', 'update-role', 'delete-role',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $removedAbilities = Ability::whereNotIn('name', $this->abilities)->pluck('id');
        DB::table('ability_role')->whereIn('ability_id', $removedAbilities)->delete();
        Ability::whereIn('id', $removedAbilities)->delete();
        $presentAbilities = Ability::whereIn('name', $this->abilities)->get();
        $absentAbilities = $presentAbilities->isEmpty() ? $this->abilities : array_diff($this->abilities, $presentAbilities->pluck('name')->toArray());
        if ($absentAbilities) {
            $absentAbilities = array_map(function ($ability) {
                return ['name' => $ability];
            }, $absentAbilities);
            Ability::insert($absentAbilities);
        }
    }
}
