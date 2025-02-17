<?php

namespace App\Tests\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FilmControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $filmRepository;
    private string $path = '/film/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->filmRepository = $this->manager->getRepository(Film::class);

        foreach ($this->filmRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Film index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'film[title]' => 'Testing',
            'film[description]' => 'Testing',
            'film[releaseDate]' => 'Testing',
            'film[imagePath]' => 'Testing',
            'film[genre]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->filmRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setReleaseDate('My Title');
        $fixture->setImagePath('My Title');
        $fixture->setGenre('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Film');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setReleaseDate('Value');
        $fixture->setImagePath('Value');
        $fixture->setGenre('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'film[title]' => 'Something New',
            'film[description]' => 'Something New',
            'film[releaseDate]' => 'Something New',
            'film[imagePath]' => 'Something New',
            'film[genre]' => 'Something New',
        ]);

        self::assertResponseRedirects('/film/');

        $fixture = $this->filmRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getReleaseDate());
        self::assertSame('Something New', $fixture[0]->getImagePath());
        self::assertSame('Something New', $fixture[0]->getGenre());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Film();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setReleaseDate('Value');
        $fixture->setImagePath('Value');
        $fixture->setGenre('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/film/');
        self::assertSame(0, $this->filmRepository->count([]));
    }
}
