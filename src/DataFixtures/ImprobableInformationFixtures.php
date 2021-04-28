<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\ImprobableInformation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ImprobableInformationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i < 50; $i++) {

            $information = new ImprobableInformation();
            $information->setDescription($faker->text(255));
            $manager->persist($information);

        }

        $manager->flush();
        
    }
}
