<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(3)->create()->each(
            function ($user) {
                Profile::factory()->count(1)->create(['user_id' => $user->id]);
//                Transaction::factory()->count(rand(1000, 2000))->create(['user_id' => $user->id]);
            }
        );
    }
}
