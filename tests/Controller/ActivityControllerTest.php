<?php

namespace App\Test\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ActivityRepository $repository;
    private string $path = '/activity/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Activity::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Activity index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'activity[name]' => 'Testing',
            'activity[description]' => 'Testing',
            'activity[price]' => 'Testing',
            'activity[address]' => 'Testing',
            'activity[latitude]' => 'Testing',
            'activity[longitude]' => 'Testing',
            'activity[created_at]' => 'Testing',
            'activity[updated_at]' => 'Testing',
            'activity[category_id]' => 'Testing',
        ]);

        self::assertResponseRedirects('/activity/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Activity();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrice('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setCategory_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Activity');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Activity();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrice('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setCategory_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'activity[name]' => 'Something New',
            'activity[description]' => 'Something New',
            'activity[price]' => 'Something New',
            'activity[address]' => 'Something New',
            'activity[latitude]' => 'Something New',
            'activity[longitude]' => 'Something New',
            'activity[created_at]' => 'Something New',
            'activity[updated_at]' => 'Something New',
            'activity[category_id]' => 'Something New',
        ]);

        self::assertResponseRedirects('/activity/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getLatitude());
        self::assertSame('Something New', $fixture[0]->getLongitude());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUpdated_at());
        self::assertSame('Something New', $fixture[0]->getCategory_id());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Activity();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrice('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLatitude('My Title');
        $fixture->setLongitude('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setCategory_id('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/activity/');
    }
}
