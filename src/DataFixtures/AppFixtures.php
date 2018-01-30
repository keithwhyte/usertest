<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 5 users
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFirstName('John'.$i);
            $user->setLastName('Smith'.$i);
            $user->setEmail('john.smith'.$i.'@testmail.com');
            $user->setDepartment('Department'.$i);
            $user->setActive(true);
            $manager->persist($user);
        }

        $manager->flush();
    }
}