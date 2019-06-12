<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        \DB::table('roles')->insert([
            0 => [
                'id'        => 1,
                'rol'      => 'Administrador',
                'created_at' => '2019-06-10 00:00:00',
                'updated_at' => '2019-06-10 00:00:00',
            ],
            1 => [
                'id'        => 2,
                'rol'      => 'Almacen',
                'created_at' => '2019-06-10 00:00:00',
                'updated_at' => '2019-06-10 00:00:00',
            ],
            2 => [
                'id'        => 3,
                'rol'      => 'Oficina',
                'created_at' => '2019-06-10 00:00:00',
                'updated_at' => '2019-06-10 00:00:00',
            ]
        ]);

        \DB::table('users')->insert([
            0 => [
                'id'        => 1,
                'role_id'   => 1,
                'name'      => 'Genaro',
                'last_name' => 'Perez', 
                'user_name' => 'genaro-perez',
                'email'     => 'genaro@gmail.com',
                'password'  => bcrypt('genaro'),
                'created_at' => '2019-06-10 00:00:00',
                'updated_at' => '2019-06-10 00:00:00'
            ],
        ]);
    }
}
