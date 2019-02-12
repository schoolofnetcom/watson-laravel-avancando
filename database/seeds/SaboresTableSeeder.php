<?php

use Illuminate\Database\Seeder;
use App\Sabor;

class SaboresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sabor = new Sabor;
        $sabor->title = 'Calabreza';
        $sabor->price = 15;
        $sabor->save();

        $sabor = new Sabor;
        $sabor->title = 'Mussarela';
        $sabor->price = 10;
        $sabor->save();
    }
}
