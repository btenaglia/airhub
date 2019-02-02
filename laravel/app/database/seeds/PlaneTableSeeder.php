<?php

use App\Models\Plane;

class PlaneTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();

        $plane = new Plane();
        $plane->setName('Plane N660W Nº1');
        $plane->setType('N660W');
        $plane->setIdentifier('C402');
        $plane->setSeatsLimit(15);
        $plane->setWeightLimit(500);
        $plane->save();
        
        $this->command->info('Plane table seeded!');
    }
}
