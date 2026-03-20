<?php

namespace App\DataFixtures;

use App\Entity\Parametre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParametreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $param1 = new Parametre();
        $param1->setLibelle('Durée du jeu (minutes)');
        $param1->setValeur('60');
        $manager->persist($param1);

        $manager->flush();
    }
}
