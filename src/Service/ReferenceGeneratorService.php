<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class ReferenceGeneratorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateReference(): string
    {
        $referenceExists = true;
        $reference = '';

        while ($referenceExists) {
            $reference = 'Slab' . $this->generateRandomString(10);
            $order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
            $referenceExists = ($order !== null);
        }

        return $reference;
    }

    private function generateRandomString($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
