<?php

namespace App\DataFixtures;

use App\Entity\Vignette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VignettesFixtures extends Fixture
{
    public const VIGNETTE_SLAM = 'vignette_slam';
    public const VIGNETTE_SISR = 'vignette_sisr';
    public const VIGNETTE_CYBER = 'vignette_cyber';
    public const VIGNETTE_IA = 'vignette_ia';

    public function load(ObjectManager $manager): void
    {
        $vignettesData = [
            ['nom' => 'Vignette SLAM', 'imagePath' => 'public/images/Vignettes/Vignette_SLAM.jpg', 'ref' => self::VIGNETTE_SLAM],
            ['nom' => 'Vignette SISR', 'imagePath' => 'public/images/Vignettes/Vignette_SISR.jpg', 'ref' => self::VIGNETTE_SISR],
            ['nom' => 'Vignette Cyber', 'imagePath' => 'public/images/Vignettes/Vignette_Cyber.jpg', 'ref' => self::VIGNETTE_CYBER],
            ['nom' => 'Vignette IA', 'imagePath' => 'public/images/Vignettes/Vignette_IA.jpg', 'ref' => self::VIGNETTE_IA],
        ];

        foreach ($vignettesData as $data) {
            $vignette = new Vignette();
            $vignette->setInformation($data['nom']);
            $vignette->setImage($data['imagePath']);
            $manager->persist($vignette);
            $this->addReference($data['ref'], $vignette);
        }

        $manager->flush();
    }
}
