<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Type;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public const TYPE_PUZZLE = 'type_puzzle';
    public const TYPE_QUIZ = 'type_quiz';
    public const TYPE_DIFFERENCE = 'type_difference';
    public const TYPE_CRYPTOGRAPHIE = 'type_cryptographie';
    public const TYPE_REPONSE_OUVERTE = 'type_reponse_ouverte';
    public const TYPE_CASSE_TETE = 'type_casse_tete';
    public const TYPE_VISUEL = 'type_visuel';
    public const TYPE_LOGIQUE = 'type_logique';
    public const TYPE_MOTS_MELES = 'type_mots_meles';

    public function load(ObjectManager $manager): void
    {
        $type1 = new Type();
        $type1->setLibelle('QUIZ');
        $manager->persist($type1);
        $this->addReference(self::TYPE_QUIZ, $type1);

        $type2 = new Type();
        $type2->setLibelle('Différence d\'images');
        $manager->persist($type2);
        $this->addReference(self::TYPE_DIFFERENCE, $type2);

        $type3 = new Type();
        $type3->setLibelle('Réponse ouverte');
        $manager->persist($type3);
        $this->addReference(self::TYPE_REPONSE_OUVERTE, $type3);

        $manager->flush();
    }
}
