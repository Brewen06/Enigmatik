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
            ['nom' => 'Le Robot', 'imagePath' => 'public/images/avatars/robot.svg'],
            ['nom' => 'Le Ninja', 'imagePath' => 'public/images/avatars/ninja.svg'],
            ['nom' => 'Le Sorcier', 'imagePath' => 'public/images/avatars/wizard.svg'],
            ['nom' => 'L\'Alien', 'imagePath' => 'public/images/avatars/alien.svg'],
            ['nom' => 'Le Pirate', 'imagePath' => 'public/images/avatars/pirate.svg'],
            ['nom' => 'Le Chevalier', 'imagePath' => 'public/images/avatars/knight.svg'],
            ['nom' => 'L\'Astronaute', 'imagePath' => 'public/images/avatars/astronaut.svg'],
            ['nom' => 'Le Super-Héros', 'imagePath' => 'public/images/avatars/superhero.svg'],
            ['nom' => 'Le Zombie', 'imagePath' => 'public/images/avatars/zombie.svg'],
            ['nom' => 'Le Vampire', 'imagePath' => 'public/images/avatars/vampire.svg'],
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
