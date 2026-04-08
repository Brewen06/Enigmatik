<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Type;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public const TYPE_QUIZ = 'type_quiz';
    public const TYPE_REPONSE_OUVERTE = 'type_reponse_ouverte';
    public const TYPE_QCM = 'type_qcm';
    public const TYPE_VRAI_FAUX = 'type_vrai_faux';
    public const TYPE_FRISE = 'type_frise';
    public const TYPE_CODE = 'type_code';

    public function load(ObjectManager $manager): void
    {
        $type1 = new Type();
        $type1->setLibelle('QUIZ');
        $type1->setImageUsage(Type::IMAGE_USAGE_NONE);
        $manager->persist($type1);
        $this->addReference(self::TYPE_QUIZ, $type1);

        $type2 = new Type();
        $type2->setLibelle('Réponse ouverte');
        $type2->setImageUsage(Type::IMAGE_USAGE_NONE);
        $manager->persist($type2);
        $this->addReference(self::TYPE_REPONSE_OUVERTE, $type2);

        $type3 = new Type();
        $type3->setLibelle('QCM');
        $type3->setImageUsage(Type::IMAGE_USAGE_NONE);
        $manager->persist($type3);
        $this->addReference(self::TYPE_QCM, $type3);

        $type4 = new Type();
        $type4->setLibelle('Vrai/Faux');
        $type4->setImageUsage(Type::IMAGE_USAGE_NONE);
        $manager->persist($type4);
        $this->addReference(self::TYPE_VRAI_FAUX, $type4);

        $type5 = new Type();
        $type5->setLibelle('frise');
        $type5->setImageUsage(Type::IMAGE_USAGE);
        $manager->persist($type5);
        $this->addReference(self::TYPE_FRISE, $type5);

        $type6 = new Type();
        $type6->setLibelle('code');
        $type6->setImageUsage(Type::IMAGE_USAGE_NONE);
        $manager->persist($type6);
        $this->addReference('type_code', $type6);

        $manager->flush();
    }
}
