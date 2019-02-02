<?php

use App\Models\User;

class UserTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();

        $adminUser = new User();
        $adminUser->setCompleteName("Default Admin User");
        $adminUser->setEmail("admin@example.com");
        $adminUser->setPassword(\Crypt::encrypt('password'));
        $adminUser->setUserType(User::USER_TYPE_ADMIN);
        $adminUser->save();
        
        $user1 = new User();
        $user1->setCompleteName("Emilio Genesio");
        $user1->setEmail("emiliogenesio@gmail.com");
        $user1->setAddress("Address example");
        $user1->setCellPhone("Cell phone 1234567");
        $user1->setPassword(\Crypt::encrypt('password'));
        $user1->setUserType(User::USER_TYPE_APP);
        $user1->save();
        
        $this->command->info('User table seeded!');
    }
}
