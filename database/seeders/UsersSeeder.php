<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Se crea un usuario por cada rol y en el primer team que devuelva la query
         */
        $roles = Role::all();
        $team = Team::whereNotNull('name')->first();

        if(!User::whereNotNull('email')->first()){
            foreach($roles as $role){
                User::create([
                    'name' => 'Usuario '.$role->name,
                    'email' => 'user_'.$role->name.'@gettaskuser.com',
                    'password' => Hash::make("12345678"),
                    'status'   => 'active',
                    'is_validated' => true,
                    'team_id' => $team->id,
                    'role_id' => $role->id
                ]);
            }
        }

    }
}
