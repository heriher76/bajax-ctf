<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// for ($i=1; $i<=20 ; $i++) { 
     //    $user = User::create([
     //        'email' => "eam24maret@gmail.com$i",
     //        'password' => Hash::make("ersaazis"),

     //        'name' => "$i Ersa Azis Mansyur",
     //        'birthplace' => "Garut",
     //        'dateofbirth' => "1998-03-24",
     //        'aboutme' => "Learn Anything Share Anything",
     //        'address' => "WAKANDA",
     //        'website' => "http://ersaazis.github.io",
     //    ]);
     //    $user->assignRole([1]);
     //    $file=public_path()."/svg/gambarasli.jpg";
     //    $user->copyMedia($file)->toMediaCollection('avatars');
    	// }
        $user = User::create([
            'email' => "eam24maret@gmail.com",
            'password' => Hash::make("ersaazis"),

            'name' => "Ersa Azis Mansyur",
            'birthplace' => "Garut",
            'dateofbirth' => "1998-03-24",
            'aboutme' => "Learn Anything Share Anything",
            'address' => "WAKANDA",
            'website' => "http://ersaazis.github.io",
	     	]);
        $user->assignRole([1]);
    }
}
