<?php

use App\Entity\Activity;
use App\Service\FilterService;
use App\Service\ReviewService;
use PHPUnit\Framework\TestCase;
use App\Service\SearchFormService;
use App\Repository\ReviewRepository;
use App\Repository\ServiceRepository;
use App\Controller\ActivityController;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationTest extends WebTestCase
{
    private $client;
    private $reservationRepository;
    public function testJsonEncoding()
    {
        $reservations = [];

        $currentDateTime = new DateTime();

        $reservationsFormat = [];
        foreach ($reservations as $reservation) {
            if (!$reservation->isStatus() && $reservation->getReservationStart() > $currentDateTime) {
                $reservationsFormat[] = [
                    'id' => $reservation->getId(),
                    'start' => $reservation->getReservationStart()->format('Y-m-d H:i'),
                    'end' => $reservation->getReservationEnd()->format('Y-m-d H:i'),
                    'status' => $reservation->isStatus(),
                    'activity_id' => $reservation->getActivity()->getId(),
                    'backgroundColor' => '#759D88',
                    'textColor' => '#FFFFFF',
                ];
            }
        }

        $reservationsJson = json_encode($reservationsFormat);

        $this->assertIsString($reservationsJson, 'Le résultat doit être une chaîne de caractères JSON');
        $this->assertJson($reservationsJson, 'Le résultat doit être une chaîne JSON valide');
    } 
}
