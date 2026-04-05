<?php

namespace App\DataFixtures;

use App\Entity\Enigme;
use App\Entity\Jeu;
use App\Entity\Type;
use App\Entity\Vignette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EnigmeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jeu = $this->getReference(JeuFixtures::JEU_REFERENCE, Jeu::class);

        // Enigme 1: Algorithmique
        $enigme1 = new Enigme();
        $enigme1->setJeu($jeu);
        $enigme1->setTitre('Logique Conditionnelle');
        $enigme1->setConsigne("CONTEXTE :\nUn programme contient la logique suivante :\nVariable Score = 15\nSI Score > 10 ALORS AFFICHER 'Bravo'\nSINON AFFICHER 'Perdu'\n\nQUESTION :\nQuel mot exact sera affiché à l'écran ?");
        $enigme1->setIndice('Bravo');
        $enigme1->setSolution('Bravo');
        $enigme1->setCodeReponse('A1');
        $enigme1->setOrdre(1);
        $enigme1->setType($this->getReference(TypeFixtures::TYPE_REPONSE_OUVERTE, Type::class));
        $enigme1->setVignette($this->getReference(VignettesFixtures::VIGNETTE_SLAM, Vignette::class));
        $manager->persist($enigme1);

        // Enigme 2: Hardware
        $enigme2 = new Enigme();
        $enigme2->setJeu($jeu);
        $enigme2->setTitre('Mémoire Vive');
        $enigme2->setConsigne("CONTEXTE :\nCe composant est essentiel pour la rapidité de l'ordinateur. Il stocke temporairement les données des programmes en cours d'exécution, mais se vide totalement lorsque l'ordinateur est éteint.\n\nQUESTION :\nQuel est son acronyme en 3 lettres ?");
        $enigme2->setIndice('RAM');
        $enigme2->setSolution('RAM');
        $enigme2->setCodeReponse('B2');
        $enigme2->setOrdre(2);
        $enigme2->setChoices(['CPU', 'RAM', 'SSD', 'GPU']);
        $enigme2->setType($this->getReference(TypeFixtures::TYPE_QUIZ, Type::class));
        $enigme2->setVignette($this->getReference(VignettesFixtures::VIGNETTE_SISR, Vignette::class));
        $manager->persist($enigme2);

        // Enigme 3: Cybersécurité
        $enigme3 = new Enigme();
        $enigme3->setJeu($jeu);
        $enigme3->setTitre('L\'Hameçonnage');
        $enigme3->setConsigne("CONTEXTE :\nVous recevez un email semblant provenir de votre banque, vous demandant de cliquer sur un lien pour vérifier votre mot de passe. C'est une technique d'attaque courante.\n\nQUESTION :\nQuel est le nom anglais de cette cyberattaque ?");
        $enigme3->setIndice('Phishing');
        $enigme3->setSolution('Phishing');
        $enigme3->setCodeReponse('C3');
        $enigme3->setOrdre(3);
        $enigme3->setType($this->getReference(TypeFixtures::TYPE_REPONSE_OUVERTE, Type::class));
        $enigme3->setVignette($this->getReference(VignettesFixtures::VIGNETTE_CYBER, Vignette::class));
        $manager->persist($enigme3);

        // Enigme 4: Intelligence Artificielle
        $enigme4 = new Enigme();
        $enigme4->setJeu($jeu);
        $enigme4->setTitre('Apprentissage Machine');
        $enigme4->setConsigne("CONTEXTE :\nContrairement à la programmation classique où l'humain écrit toutes les règles, cette branche de l'IA permet aux ordinateurs d'apprendre par eux-mêmes à partir de données (ex: reconnaître un chat sur une photo).\n\nQUESTION :\nComment appelle-t-on cette technologie (en anglais, 2 mots) ?");
        $enigme4->setIndice('Machine Learning');
        $enigme4->setSolution('Machine Learning');
        $enigme4->setCodeReponse('D4');
        $enigme4->setOrdre(4);
        $enigme4->setChoices(['Deep Learning', 'Machine Learning', 'Data Science', 'Big Data']);
        $enigme4->setType($this->getReference(TypeFixtures::TYPE_QUIZ, Type::class));
        $enigme4->setVignette($this->getReference(VignettesFixtures::VIGNETTE_IA, Vignette::class));
        $manager->persist($enigme4);

        // Enigme 5: Réseau
        $enigme5 = new Enigme();
        $enigme5->setJeu($jeu);
        $enigme5->setTitre('Le Web');
        $enigme5->setConsigne("CONTEXTE :\nC'est le protocole sécurisé utilisé pour naviguer sur internet. Vous voyez souvent un petit cadenas à côté de l'adresse du site web.\n\nQUESTION :\nQuel est ce protocole (5 lettres) ?");
        $enigme5->setIndice('HTTPS');
        $enigme5->setSolution('HTTPS');
        $enigme5->setCodeReponse('E5');
        $enigme5->setOrdre(5);
        $enigme5->setChoices(['HTTP', 'HTML', 'HTTPS', 'FTP']);
        $enigme5->setType($this->getReference(TypeFixtures::TYPE_QUIZ, Type::class));
        $enigme5->setVignette($this->getReference(VignettesFixtures::VIGNETTE_SISR, Vignette::class));
        $manager->persist($enigme5);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            JeuFixtures::class,
            TypeFixtures::class,
            VignettesFixtures::class,
        ];
    }
}
