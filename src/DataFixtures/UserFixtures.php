<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@mail.com');
        $user->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('prof@mail.com');
        $user2->setPassword(password_hash('prof', PASSWORD_BCRYPT));
        $user2->setRoles(['ROLE_PROF']);
        $manager->persist($user2);
        $manager->flush();
    }
}
