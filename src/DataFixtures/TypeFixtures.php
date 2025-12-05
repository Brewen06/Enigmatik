<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Type;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $type1 = new Type();
        $type1->setLibelle('Puzzle');
        $manager->persist($type1);

        $type2 = new Type();
        $type2->setLibelle('Quiz');
        $manager->persist($type2);

        $type3 = new Type();
        $type3->setLibelle('Différence d\'images');
        $manager->persist($type3);

        $type4 = new Type();
        $type4->setLibelle('Cryptographie');
        $manager->persist($type4);

        $type5 = new Type();
        $type5->setLibelle('Réponse ouverte');
        $manager->persist($type5);

        $type6 = new Type();
        $type6->setLibelle('Casse-tête');
        $manager->persist($type6);

        $type7 = new Type();
        $type7->setLibelle('Visuel schématique');
        $manager->persist($type7);

        $type8 = new Type();
        $type8->setLibelle('Jeu de logique');
        $manager->persist($type8);

        $manager->flush();
    }
}
