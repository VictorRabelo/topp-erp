<?php

use App\User;
use Illuminate\Database\Seeder;

class userSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->nome = 'SIG';
        $user->sobrenome = 'Automação';
        $user->empresa_id = 1;
        $user->email = 'demo@demo.com';
        $user->password = bcrypt('1234');
        $user->save();
    }
}
