<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\VocabularyWord;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VocabularyWordFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i < 50; $i++) {

            $word = new VocabularyWord();
            $word->setWord($faker->word());
            $word->setDefinition($faker->text(255));
            $manager->persist($word);

        }

        $manager->flush();
        
    }
}
