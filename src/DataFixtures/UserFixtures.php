<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('demo@apipen.fr');
        $hashedPassword = password_hash('azerty', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}