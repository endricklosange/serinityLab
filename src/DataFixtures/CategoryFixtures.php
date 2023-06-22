<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            [
                'name' => 'Massage',
                'image' => 'imgMassageCat.png',
                'logo' => 'logoMassage.png'
            ], [
                'name' => 'Spa',
                'image' => 'imgSpaCat.png',
                'logo' => 'logoSpa.png'
            ], [
                'name' => 'Salle de sport',
                'image' => 'imgGymCat.png',
                'logo' => 'logoGym.png'
            ], [
                'name' => 'Salon de beauté',
                'image' => 'imgSalonCat.png',
                'logo' => 'logoSalon.png'
            ]
        ];

        foreach ($categories as $category) {
            $categoryEntity = new Category();
            $categoryEntity->setName($category['name']);
            $categoryEntity->setLogo($category['logo']);
            $categoryEntity->setImage($category['image']);

            $manager->persist($categoryEntity);
        }

        $manager->flush();
    }
    public function getOrder()
    {
        return 1;
    }
}
