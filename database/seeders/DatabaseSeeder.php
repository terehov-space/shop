<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Translation\t;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CarouselSeeder::class);

        User::updateOrCreate([
            'email' => 'admin@project.ru',
        ], [
            'name' => 'admin',
            'password' => Hash::make('nopasswd'),
        ]);
    }
}
