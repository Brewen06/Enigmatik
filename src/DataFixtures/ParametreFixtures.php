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

        $param2 = new Parametre();
        $param2->setLibelle('Nombre d\'indices max');
        $param2->setValeur('3');
        $manager->persist($param2);

        $param3 = new Parametre();
        $param3->setLibelle('Pénalité par erreur (secondes)');
        $param3->setValeur('30');
        $manager->persist($param3);

        $manager->flush();
    }
}
