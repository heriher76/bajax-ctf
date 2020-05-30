<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permissions = [
           'role-list', //1
           'role-create', //2
           'role-edit', //3
           'role-delete', //4

           'user-list', //5
           'user-create', //6
           'user-edit', //7
           'user-delete', //8

           'kas-list', //9
           'kas-edit', //10

           'keuangan-list', //11
           'keuangan-create', //12
           'keuangan-edit', //13
           'keuangan-delete', //14

           'challenge-create', //15
           'challenge-edit', //16
           'challenge-delete', //17

        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
