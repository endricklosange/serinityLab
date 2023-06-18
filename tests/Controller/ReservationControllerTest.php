<?php

namespace App\Test\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ReservationRepository $repository;
    private string $path = '/reservation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Reservation::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reservation index');

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
            'reservation[service_id]' => 'Testing',
            'reservation[user_id]' => 'Testing',
            'reservation[reservation_start]' => 'Testing',
            'reservation[reservation_end]' => 'Testing',
            'reservation[status]' => 'Testing',
            'reservation[pay]' => 'Testing',
            'reservation[created_at]' => 'Testing',
            'reservation[updated_at]' => 'Testing',
        ]);

        self::assertResponseRedirects('/reservation/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reservation();
        $fixture->setService_id('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setReservation_start('My Title');
        $fixture->setReservation_end('My Title');
        $fixture->setStatus('My Title');
        $fixture->setPay('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reservation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reservation();
        $fixture->setService_id('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setReservation_start('My Title');
        $fixture->setReservation_end('My Title');
        $fixture->setStatus('My Title');
        $fixture->setPay('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reservation[service_id]' => 'Something New',
            'reservation[user_id]' => 'Something New',
            'reservation[reservation_start]' => 'Something New',
            'reservation[reservation_end]' => 'Something New',
            'reservation[status]' => 'Something New',
            'reservation[pay]' => 'Something New',
            'reservation[created_at]' => 'Something New',
            'reservation[updated_at]' => 'Something New',
        ]);

        self::assertResponseRedirects('/reservation/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getService_id());
        self::assertSame('Something New', $fixture[0]->getUser_id());
        self::assertSame('Something New', $fixture[0]->getReservation_start());
        self::assertSame('Something New', $fixture[0]->getReservation_end());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getPay());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUpdated_at());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Reservation();
        $fixture->setService_id('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setReservation_start('My Title');
        $fixture->setReservation_end('My Title');
        $fixture->setStatus('My Title');
        $fixture->setPay('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/reservation/');
    }
}
