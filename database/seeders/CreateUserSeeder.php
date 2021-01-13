<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name'=>'admin',
                'phone'=>'+265880295692',
                'is_admin'=>'1',
                'password'=>bcrypt('123456'),
            ],
            [
                'name'=>'user',
                'phone'=>'+265880295690',
                'is_admin'=>'0',
                'password'=>bcrypt('123456'),
            ],
        ];

        foreach($user as $key => $value){
            User::create($value);
        }
    }
}
