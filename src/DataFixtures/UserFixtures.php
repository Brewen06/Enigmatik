<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $data = new User();
        $data->setRoles(['ROLE_ADMIN']);
        $data->setNom('admin');
        $data->setEmail('admin@mail.com');
        $data->setPassword($this->passwordEncoder->hashPassword($data, 'Adminmatik2025!'));
        $manager->persist($data);

        $data = new User();
        $data->setRoles(['ROLE_PROF']);
        $data->setNom('prof');
        $data->setEmail('prof@mail.com');
        $data->setPassword($this->passwordEncoder->hashPassword($data, 'Profmatik2025!'));
        $manager->persist($data);

        $manager->flush();
    }
}
