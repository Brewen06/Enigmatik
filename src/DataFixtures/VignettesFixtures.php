<?php

namespace App\DataFixtures;

use App\Entity\Vignette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VignettesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $vignettesData = [
            ['nom' => 'Vignette SLAM', 'imagePath' => 'public/images/Vignettes/Vignette_SLAM.jpg'],
            ['nom' => 'Vignette SISR', 'imagePath' => 'public/images/Vignettes/Vignette_SISR.jpg'],
            ['nom' => 'Vignette Cyber', 'imagePath' => 'public/images/Vignettes/Vignette_Cyber.jpg'],
            ['nom' => 'Vignette IA', 'imagePath' => 'public/images/Vignettes/Vignette_IA.jpg'],
        ];

        foreach ($vignettesData as $data) {
            $vignette = new Vignette();
            $vignette->setInformation($data['nom']);
            $vignette->setImage($data['imagePath']);
            $manager->persist($vignette);
        }

        $manager->flush();
    }
}
