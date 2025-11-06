<?php

namespace App\Tests\Controller;

use App\Entity\Jeu;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JeuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $jeuRepository;
    private string $path = '/jeu/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->jeuRepository = $this->manager->getRepository(Jeu::class);

        foreach ($this->jeuRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Jeu index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'jeu[titre]' => 'Testing',
            'jeu[messageDeBienvenue]' => 'Testing',
            'jeu[imageBienvenue]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->jeuRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Jeu();
        $fixture->setTitre('My Title');
        $fixture->setMessageDeBienvenue('My Title');
        $fixture->setImageBienvenue('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Jeu');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Jeu();
        $fixture->setTitre('Value');
        $fixture->setMessageDeBienvenue('Value');
        $fixture->setImageBienvenue('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'jeu[titre]' => 'Something New',
            'jeu[messageDeBienvenue]' => 'Something New',
            'jeu[imageBienvenue]' => 'Something New',
        ]);

        self::assertResponseRedirects('/jeu/');

        $fixture = $this->jeuRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getMessageDeBienvenue());
        self::assertSame('Something New', $fixture[0]->getImageBienvenue());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Jeu();
        $fixture->setTitre('Value');
        $fixture->setMessageDeBienvenue('Value');
        $fixture->setImageBienvenue('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/jeu/');
        self::assertSame(0, $this->jeuRepository->count([]));
    }
}
