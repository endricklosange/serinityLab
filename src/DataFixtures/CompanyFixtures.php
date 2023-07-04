<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faker\Factory;

// src/DataFixtures/CompanyFixtures.php

// ...

class CompanyFixtures extends Fixture implements OrderedFixtureInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer une entreprise pour chaque utilisateur
        $users = $this->userRepository->findAll();
        foreach ($users as $index => $user) { // Ajouter la variable $index pour obtenir l'index de la boucle
            $company = new Company();
            $company->setCompanyName($faker->company);

            // Lier l'entreprise à l'utilisateur
            $user->setCompany($company);

            $manager->persist($company);
            $manager->persist($user);

            // Définir une référence pour chaque entreprise
            $this->addReference('company_' . ($index + 1), $company);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}

