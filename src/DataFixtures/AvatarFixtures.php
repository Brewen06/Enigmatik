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
            ['nom' => 'Le Robot', 'imagePath' => 'images/avatars/robot.svg'],
            ['nom' => 'Le Ninja', 'imagePath' => 'images/avatars/ninja.svg'],
            ['nom' => 'Le Sorcier', 'imagePath' => 'images/avatars/wizard.svg'],
            ['nom' => 'L\'Alien', 'imagePath' => 'images/avatars/alien.svg'],
            ['nom' => 'Le Pirate', 'imagePath' => 'images/avatars/pirate.svg'],
            ['nom' => 'Le Chevalier', 'imagePath' => 'images/avatars/knight.svg'],
            ['nom' => 'L\'Astronaute', 'imagePath' => 'images/avatars/astronaut.svg'],
            ['nom' => 'Le Super-Héros', 'imagePath' => 'images/avatars/superhero.svg'],
            ['nom' => 'Le Zombie', 'imagePath' => 'images/avatars/zombie.svg'],
            ['nom' => 'Le Vampire', 'imagePath' => 'images/avatars/vampire.svg'],
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
