<?php

use Illuminate\Database\Seeder;
use App\Ability;
use Illuminate\Support\Facades\DB;

class AbilitySeeder extends Seeder
{
    public $abilities = [
        'view-any-user', 'view-user', 'create-user', 'update-user', 'delete-user',
        'view-any-role', 'view-role', 'create-role', 'update-role', 'delete-role',
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deletedAbilities = Ability::whereNotIn('name', $this->abilities)->pluck('id');
        DB::table('ability_role')->whereIn('ability_id', $deletedAbilities)->delete();
        Ability::whereIn('id', $deletedAbilities)->delete();
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
