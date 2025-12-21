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
        $type1->setLibelle('Puzzle');
        $manager->persist($type1);
        $this->addReference(self::TYPE_PUZZLE, $type1);

        $type2 = new Type();
        $type2->setLibelle('Quiz');
        $manager->persist($type2);
        $this->addReference(self::TYPE_QUIZ, $type2);

        $type3 = new Type();
        $type3->setLibelle('Différence d\'images');
        $manager->persist($type3);
        $this->addReference(self::TYPE_DIFFERENCE, $type3);

        $type4 = new Type();
        $type4->setLibelle('Cryptographie');
        $manager->persist($type4);
        $this->addReference(self::TYPE_CRYPTOGRAPHIE, $type4);

        $type5 = new Type();
        $type5->setLibelle('Réponse ouverte');
        $manager->persist($type5);
        $this->addReference(self::TYPE_REPONSE_OUVERTE, $type5);

        $type6 = new Type();
        $type6->setLibelle('Casse-tête');
        $manager->persist($type6);
        $this->addReference(self::TYPE_CASSE_TETE, $type6);

        $type7 = new Type();
        $type7->setLibelle('Visuel schématique');
        $manager->persist($type7);
        $this->addReference(self::TYPE_VISUEL, $type7);

        $type8 = new Type();
        $type8->setLibelle('Jeu de logique');
        $manager->persist($type8);
        $this->addReference(self::TYPE_LOGIQUE, $type8);

        $type9 = new Type();
        $type9->setLibelle('Mots mêlés');
        $manager->persist($type9);
        $this->addReference(self::TYPE_MOTS_MELES, $type9);

        $manager->flush();
    }
}
