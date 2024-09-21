<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=\App\Models\User::create(
        [
            'name'=>'super admin',
            'email'=>'super_admin@gmail.com',
            'password' => Hash::make('secret1234'),
            'user_type'=>'super_admin',
        ]);

    }
}

