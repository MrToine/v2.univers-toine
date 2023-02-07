<?php

namespace App\DataFixtures;

use App\Entity\News;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use App\DataFixtures;

class AppFixtures extends Fixture
{
    private Generator $faker;
    
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {


        for ($i=0; $i < 10; $i++) { 
            // code...
            $news = new News();
            $news->setTitle($this->faker->word())
                 ->setRewritedTitle($this->faker->slug())
                 ->setContent($this->faker->paragraph())
                 ->setAuthorUserId($this->faker->randomDigit())
                 ->setPublished(1)
                 ->setIdCategory($this->faker->randomDigit())
                 ->setPublishingStartDate(0)
                 ->setPublishingEndDate(0)
                 ->setSources('a:0{}')
                 ->setViewsNumber($this->faker->randomDigit())
                 ->setSummary('')
                 ->setAuthorCustomName('')
                 ->setThumbnail($this->faker->imageUrl(640, 480, 'animals', true))
                 ->setTopListEnabled(0);
            $manager->persist($news);
        }


        $manager->flush();
    }
}
