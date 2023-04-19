<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface  $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        // Création d’un utilisateur de type “company”
        $user = new User();
        $user->setEmail('contact@serinitylab.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword( $this->passwordEncoder->hashPassword($user, 'test123456789'));
        
        $manager->persist($user);
        $manager->flush();
    }
}

