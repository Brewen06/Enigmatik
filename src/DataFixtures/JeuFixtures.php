<?php

namespace App\DataFixtures;

use App\Entity\Jeu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JeuFixtures extends Fixture
{
    public const JEU_REFERENCE = 'jeu-principal';

    public function load(ObjectManager $manager): void
    {
        $jeu = new Jeu();
        $jeu->setTitre('Mission Enigmatik');
        $jeu->setMessageDeBienvenue("Bienvenue !");
        $jeu->setCodeFinal('BRAVO123'); // Le prof peut changer ça plus tard
        
        $manager->persist($jeu);
        $this->addReference(self::JEU_REFERENCE, $jeu);

        $manager->flush();
    }
}
