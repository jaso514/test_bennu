<?php

use Illuminate\Database\Seeder;

class UsersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i<=50; $i++) {
          DB::table('users')->insert([
              'nro_cliente' => str_pad($i, 5, "0", STR_PAD_LEFT),
              'nro_documento' => rand(100000, 99999999)
            ]);
        }
    }
}
