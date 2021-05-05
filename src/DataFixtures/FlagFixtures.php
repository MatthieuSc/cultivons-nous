<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Flag;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FlagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i < 50; $i++) {

            $flag = new Flag();
            $flag->setCountry($faker->word(1));
            $flag->setName($faker->word(1));
            $flag->setFlag('public/images/drapeaux/France');
            $manager->persist($flag);

        }

        $manager->flush();
        
    }
}
