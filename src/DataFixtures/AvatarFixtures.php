<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AvatarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $avatarsData = [
            ['nom' => 'Le robot', 'imagePath' => 'images/avatars/robot.svg'],
            ['nom' => 'Le ninja', 'imagePath' => 'images/avatars/ninja.svg'],
            ['nom' => 'Le sorcier', 'imagePath' => 'images/avatars/wizard.svg'],
            ['nom' => 'L\'alien', 'imagePath' => 'images/avatars/alien.svg'],
            ['nom' => 'Le pirate', 'imagePath' => 'images/avatars/pirate.svg'],
            ['nom' => 'Le chevalier', 'imagePath' => 'images/avatars/knight.svg'],
            ['nom' => 'L\'astronaute', 'imagePath' => 'images/avatars/astronaut.svg'],
            ['nom' => 'Le super-héros', 'imagePath' => 'images/avatars/superhero.svg'],
            ['nom' => 'Le zombie', 'imagePath' => 'images/avatars/zombie.svg'],
            ['nom' => 'Le vampire', 'imagePath' => 'images/avatars/vampire.svg'],
        ];
        foreach ($avatarsData as $data) {
            $avatar = new Avatar();
            $avatar->setNom($data['nom']);
            $avatar->setImage($data['imagePath']);
            $manager->persist($avatar);
        }

        $manager->flush();
    }
}
