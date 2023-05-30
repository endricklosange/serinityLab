<?php

namespace App\Test\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ServiceRepository $repository;
    private string $path = '/service/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Service::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Service index');

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
            'service[name]' => 'Testing',
            'service[created_at]' => 'Testing',
            'service[updated_at]' => 'Testing',
            'service[activity_id]' => 'Testing',
        ]);

        self::assertResponseRedirects('/service/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Service();
        $fixture->setName('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setActivity_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Service');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Service();
        $fixture->setName('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setActivity_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'service[name]' => 'Something New',
            'service[created_at]' => 'Something New',
            'service[updated_at]' => 'Something New',
            'service[activity_id]' => 'Something New',
        ]);

        self::assertResponseRedirects('/service/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUpdated_at());
        self::assertSame('Something New', $fixture[0]->getActivity_id());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Service();
        $fixture->setName('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');
        $fixture->setActivity_id('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/service/');
    }
}
