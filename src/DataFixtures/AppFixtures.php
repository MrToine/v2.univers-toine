<?php

namespace App\DataFixtures;

use App\Entity\News;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use App\DataFixtures;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $user = new Member();
            $user->setDisplayName($this->faker->FirstName())
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->email())
                ->setLevel(1)
                ->setPlainPassword('password');

            $manager->persist($user);

        }


        $manager->flush();
    }
}
