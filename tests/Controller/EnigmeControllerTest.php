<?php

namespace App\Tests\Controller;

use App\Entity\Enigme;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EnigmeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $enigmeRepository;
    private string $path = '/enigme/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->enigmeRepository = $this->manager->getRepository(Enigme::class);

        foreach ($this->enigmeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Enigme index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'enigme[type]' => 'Testing',
            'enigme[ordre]' => 'Testing',
            'enigme[titre]' => 'Testing',
            'enigme[consigne]' => 'Testing',
            'enigme[codeSecret]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->enigmeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Enigme();
        $fixture->setType('My Title');
        $fixture->setOrdre('My Title');
        $fixture->setTitre('My Title');
        $fixture->setConsigne('My Title');
        $fixture->setCodeSecret('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Enigme');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Enigme();
        $fixture->setType('Value');
        $fixture->setOrdre('Value');
        $fixture->setTitre('Value');
        $fixture->setConsigne('Value');
        $fixture->setCodeSecret('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'enigme[type]' => 'Something New',
            'enigme[ordre]' => 'Something New',
            'enigme[titre]' => 'Something New',
            'enigme[consigne]' => 'Something New',
            'enigme[codeSecret]' => 'Something New',
        ]);

        self::assertResponseRedirects('/enigme/');

        $fixture = $this->enigmeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getOrdre());
        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getConsigne());
        self::assertSame('Something New', $fixture[0]->getCodeSecret());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Enigme();
        $fixture->setType('Value');
        $fixture->setOrdre('Value');
        $fixture->setTitre('Value');
        $fixture->setConsigne('Value');
        $fixture->setCodeSecret('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/enigme/');
        self::assertSame(0, $this->enigmeRepository->count([]));
    }
}
