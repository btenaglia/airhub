<?php

use App\Models\Place;

class PlaceTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();

        $place1 = new Place();
        $place1->setName('New York City');
        $place1->setShortName('NYC');
        $place1->save();

        $place2 = new Place();
        $place2->setName('Hyannis');
        $place2->setShortName('HYA');
        $place2->save();
        
        $place3 = new Place();
        $place3->setName('Nantucket Island');
        $place3->setShortName('ACK');
        $place3->save();
        
        $this->command->info('Plane table seeded!');
    }
}
